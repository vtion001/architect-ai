<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\KnowledgeBaseAsset;
use App\Models\MediaAsset;
use App\Services\ContentService;
use App\Services\TokenService;
use App\Services\SocialPublishingService;
use App\Services\BrandResolverService;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\PublishContentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentCreatorController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        protected \App\Services\ResearchService $researchService,
        protected TokenService $tokenService,
        protected SocialPublishingService $socialPublishingService,
        protected BrandResolverService $brandResolverService
    ) {}

    public function index()
    {
        $tenant = app(\App\Models\Tenant::class);
        $brands = $tenant->brands()->orderBy('is_default', 'desc')->get();

        $stats = [
            'total_content' => Content::count(),
            'this_month' => Content::whereMonth('created_at', now()->month)->count(),
            'in_draft' => Content::where('status', 'draft')->count(),
            'published' => Content::where('status', 'published')->count(),
        ];

        $recentContents = Content::where(function($q) {
            $q->whereNull('options->original_content_id')
              ->orWhere('options', '[]');
        })->latest()->take(15)->get();

        return view('content-creator.content-creator', compact('stats', 'recentContents', 'brands'));
    }

    public function store(StoreContentRequest $request)
    {
        $tokenCost = $request->getTokenCost();

        // 1. Check & Consume Tokens
        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'content_generation', ['topic' => $request->topic])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This request requires $tokenCost tokens."
            ], 402);
        }

        $options = $request->getOptions();
        $options['count'] = (int) ($options['count'] ?? 1);

        // Build context with brand guidelines via centralized service
        $context = $request->input('context');
        if ($request->filled('brand_id')) {
            $brandContext = $this->brandResolverService->buildBrandContext($request->brand_id);
            if ($brandContext) {
                $context .= $brandContext;
            }
        }

        $content = Content::create([
            'title' => $request->input('topic'),
            'topic' => $request->input('topic'),
            'type' => $request->input('type'),
            'context' => $context,
            'status' => 'generating',
            'options' => $options,
        ]);

        // Dispatch Async Generation Job
        \App\Jobs\GenerateContent::dispatch($content, auth()->user(), $tokenCost);

        return response()->json([
            'success' => true,
            'content' => $content,
            'message' => 'Content generation protocol initiated.'
        ]);
    }

    public function show(Content $content)
    {
        $path = storage_path('app/social_tokens.json');
        $isFacebookConnected = false;
        if (file_exists($path)) {
            $tokens = json_decode(file_get_contents($path), true);
            $isFacebookConnected = !empty($tokens['facebook']);
        }

        // Fetch children to determine which segments are already published
        // Check both string and int IDs as JSON storage can vary
        $children = Content::where(function($q) use ($content) {
                $q->where('options->original_content_id', $content->id)
                  ->orWhere('options->original_content_id', (string)$content->id);
            })
            ->whereIn('status', ['published', 'scheduled'])
            ->get();

        $publishedIndexes = $children->map(fn($c) => (int)($c->options['segment_index'] ?? -1))
            ->filter(fn($idx) => $idx !== -1)
            ->unique()
            ->values()
            ->toArray();
        
        // Fetch brands for Image Creator's poster mode
        $brands = auth()->user()->tenant->brands()->select('id', 'name', 'colors')->get();
        
        return view('content-creator.content-viewer', compact('content', 'isFacebookConnected', 'publishedIndexes', 'brands'));
    }

    public function getSuggestions(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|min:3',
        ]);

        $suggestions = $this->researchService->suggestSocialMediaTopics($request->topic);
        
        // RAG Discovery check
        $tenant = app(\App\Models\Tenant::class);
        $kbCount = KnowledgeBaseAsset::where('tenant_id', $tenant->id)
            ->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->topic}%")
                  ->orWhere('content', 'like', "%{$request->topic}%");
            })
            ->count();

        return response()->json([
            'suggestions' => $suggestions,
            'kb_count' => $kbCount
        ]);
    }

    public function refineContext(Request $request)
    {
        $request->validate([
            'context' => 'required|string|min:3',
        ]);

        $refined = $this->researchService->refineContext($request->context);

        return response()->json([
            'context' => trim($refined)
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,mov,avi,wmv|max:102400', // 100MB max, images & videos
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Check for Cloudinary Config
            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');

            if ($cloudName && $apiKey && $apiSecret) {
                // Upload to Cloudinary
                try {
                    $timestamp = time();
                    
                    // Signature calculation (alphabetical order of params)
                    $params = [
                        'timestamp' => $timestamp,
                    ];
                    ksort($params);
                    
                    $signParts = [];
                    foreach ($params as $key => $value) {
                        $signParts[] = "$key=$value";
                    }
                    $signString = implode('&', $signParts) . $apiSecret;
                    $signature = sha1($signString);

                    // Use attach() for file uploads (multipart/form-data)
                    // "auto" resource type handles both images and videos
                    $response = Http::attach(
                        'file', 
                        file_get_contents($file->getRealPath()), 
                        $file->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/$cloudName/auto/upload", [
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                        $url = $response->json()['secure_url'];
                        Log::info("Media uploaded to Cloudinary. URL: $url");
                        return response()->json(['success' => true, 'url' => $url]);
                    } else {
                        Log::error("Cloudinary upload failed: " . $response->status() . " - " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("Cloudinary exception: " . $e->getMessage());
                }
            } else {
                 Log::warning("Cloudinary credentials missing in .env. Falling back to public upload.");
            }

            // FALLBACK: Local Public Upload
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/content-media'), $filename);
            $url = '/uploads/content-media/' . $filename;
            
            Log::info("Media uploaded directly to public. URL: $url");

            return response()->json([
                'success' => true,
                'url' => $url,
                'message' => 'Uploaded locally. Check Cloudinary credentials if this is unintended.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    public function generateMedia(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:3',
            'format' => 'nullable|string|in:realistic,poster,asset-reference',
            'poster_text' => 'nullable|string|max:100',
            'reference_asset_url' => 'nullable|string',
            'brand_id' => 'nullable|uuid',
        ]);

        $tokenCost = 5;

        // 1. Check & Consume Tokens
        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'image_generation', ['prompt' => $request->prompt])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. Image generation requires $tokenCost tokens."
            ], 402);
        }

        $format = $request->input('format', 'realistic');
        $options = [];

        // Build format-specific options
        if ($format === 'poster') {
            $options['poster_text'] = $request->input('poster_text');
            
            // Get brand colors if brand_id provided
            if ($request->filled('brand_id')) {
                $brand = \App\Models\Brand::find($request->brand_id);
                if ($brand) {
                    $options['brand'] = [
                        'name' => $brand->name,
                        'colors' => $brand->colors,
                        'typography' => $brand->typography ?? [],
                    ];
                }
            }
        } elseif ($format === 'asset-reference') {
            $options['reference_url'] = $request->input('reference_asset_url');
        }

        // Generate image based on format
        $generatedUrl = $this->contentService->generateImage($request->prompt, $format, $options);

        if ($generatedUrl) {
            // Use CloudinaryService for upload with automatic fallback
            $cloudinaryService = app(\App\Services\CloudinaryService::class);
            $uploadResult = $cloudinaryService->uploadFromUrl($generatedUrl, 'ai-generated', 'uploads/content-media');
            
            $finalUrl = $uploadResult['url'];
            $source = match($uploadResult['source']) {
                'cloudinary' => 'ai_generation',
                'local' => 'ai_generation_local',
                default => 'ai_generation_temp',
            };

            // Index into Industrial Media Registry
            MediaAsset::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'name' => 'AI Provision: ' . Str::limit($request->prompt, 30),
                'url' => $finalUrl,
                'type' => 'image',
                'source' => $source,
                'prompt' => $request->prompt,
                'metadata' => [
                    'generator' => 'Banana Pro AI',
                    'format' => $format,
                    'timestamp' => now()->toIso8601String(),
                    'cloudinary_public_id' => $uploadResult['public_id'] ?? null,
                ]
            ]);

            return response()->json([
                'success' => true,
                'url' => $finalUrl
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Image generation failed. Please try again.'], 500);
    }

    public function regenerate(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:contents,id',
            'current_text' => 'required|string',
        ]);

        $content = Content::findOrFail($request->content_id);
        
        try {
            $options = $content->options ?? [];
            $options['count'] = 1; // Force single post generation for redo requests

            $newText = $this->contentService->generateText(
                $content->topic, 
                $content->type, 
                "REWRITE THIS POST. Original context: " . $content->context . ". \n\nCONTENT TO IMPROVE: " . $request->current_text,
                $options
            );

            return response()->json([
                'success' => true,
                'new_text' => $newText
            ]);
        } catch (\Exception $e) {
            Log::error("Regeneration failed: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Regeneration failed'], 500);
        }
    }

    public function publish(Request $request)
    {
        $validated = $request->validate([
            'content_id' => 'required|exists:contents,id',
            'segment_index' => 'required|integer',
            'final_text' => 'required|string',
            'image_url' => 'nullable|string', // Changed from url to string to support relative paths
            'platforms' => 'required|array|min:1',
            'scheduled_at' => 'required|string',
            'facebook_page_id' => 'nullable|string',
            'facebook_page_token' => 'nullable|string',
            'instagram_account_id' => 'nullable|string',
        ]);

        // Normalize image_url to full URL if it's relative
        if (!empty($validated['image_url']) && str_starts_with($validated['image_url'], '/')) {
            $validated['image_url'] = rtrim(config('app.url'), '/') . $validated['image_url'];
        }

        $scheduledAt = $validated['scheduled_at'] === 'now' ? now()->toDateTimeString() : $validated['scheduled_at'];
        
        $parentContent = Content::find($validated['content_id']);
        $results = [];
        $isImmediate = $validated['scheduled_at'] === 'now';

        $totalPlatforms = count($validated['platforms']);
        $totalTokenCost = $totalPlatforms * 5; // 5 tokens per platform post

        // 1. Check & Consume Tokens for deployment
        if (!$this->tokenService->consume(auth()->user(), $totalTokenCost, 'social_deployment', ['content_id' => $validated['content_id']])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This deployment requires $totalTokenCost tokens."
            ], 402);
        }

        foreach ($validated['platforms'] as $platform) {
            $options = [
                'platform' => $platform,
                'scheduled_at' => $scheduledAt,
                'image_url' => $validated['image_url'],
                'original_content_id' => $validated['content_id'],
                'segment_index' => (int)$validated['segment_index']
            ];

            if ($platform === 'facebook') {
                $options['page_id'] = $validated['facebook_page_id'] ?? null;
                $options['page_token'] = $validated['facebook_page_token'] ?? null;
            }
            
            if ($platform === 'instagram') {
                $options['instagram_id'] = $validated['instagram_account_id'] ?? null;
                $options['page_token'] = $validated['facebook_page_token'] ?? null;
            }

            $contentRecord = Content::create([
                'title' => ($isImmediate ? 'Published ' : 'Scheduled ') . ucfirst($platform) . ' - ' . Str::limit($parentContent->topic, 20),
                'topic' => $parentContent->topic,
                'type' => 'social-post', 
                'context' => $parentContent->context,
                'status' => 'scheduled',
                'result' => $validated['final_text'], 
                'options' => $options
            ]);

            if ($isImmediate || \Carbon\Carbon::parse($scheduledAt)->isPast()) {
                if ($platform === 'facebook' && !empty($options['page_id']) && !empty($options['page_token'])) {
                    $fbResult = $this->postToFacebook($contentRecord);
                    $results['facebook'] = $fbResult;
                    
                    if ($fbResult['success']) {
                        $currentOptions = $contentRecord->options;
                        $currentOptions['platform_post_id'] = $fbResult['id'];
                        $contentRecord->update([
                            'status' => 'published', 
                            'result' => $validated['final_text'] . "\n\n[Posted to Facebook: " . ($fbResult['id'] ?? 'Success') . "]",
                            'options' => $currentOptions
                        ]);
                    } else {
                        $contentRecord->update([
                            'status' => 'failed', 
                            'result' => $validated['final_text'] . "\n\n[Facebook Error: " . ($fbResult['error'] ?? 'Unknown Error') . "]"
                        ]);
                    }
                }

                if ($platform === 'instagram' && !empty($options['instagram_id']) && !empty($options['page_token'])) {
                    $igResult = $this->postToInstagram($contentRecord, $options['instagram_id']);
                    $results['instagram'] = $igResult;

                    if ($igResult['success']) {
                        $currentOptions = $contentRecord->options;
                        $currentOptions['platform_post_id'] = $igResult['id'];
                        $contentRecord->update([
                            'status' => 'published',
                            'result' => $validated['final_text'] . "\n\n[Posted to Instagram: " . ($igResult['id'] ?? 'Success') . "]",
                            'options' => $currentOptions
                        ]);
                    } else {
                        $contentRecord->update([
                            'status' => 'failed',
                            'result' => $validated['final_text'] . "\n\n[Instagram Error: " . ($igResult['error'] ?? 'Unknown Error') . "]"
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $isImmediate ? 'Successfully published!' : 'Content scheduled successfully.',
            'results' => $results
        ]);
    }

    /**
     * Post content to Facebook.
     * 
     * @deprecated Use SocialPublishingService::postToFacebook() directly.
     */
    private function postToFacebook(Content $content): array
    {
        return $this->socialPublishingService->postToFacebook($content);
    }

    /**
     * Post content to Instagram.
     * 
     * @deprecated Use SocialPublishingService::postToInstagram() directly.
     */
    private function postToInstagram(Content $content, string $igUserId): array
    {
        return $this->socialPublishingService->postToInstagram($content, $igUserId);
    }

    /**
     * Check if URL is a video.
     * 
     * @deprecated Use SocialPublishingService::isVideo() directly.
     */
    private function isVideo(string $url): bool
    {
        return $this->socialPublishingService->isVideo($url);
    }

    public function saveVisual(Request $request, Content $content)
    {
        $validated = $request->validate([
            'image_url' => 'required|string', // Accept both URLs and local paths
            'index' => 'required|integer'
        ]);

        $options = $content->options ?? [];
        $visuals = $options['visuals'] ?? [];
        $visuals[$validated['index']] = $validated['image_url'];
        $options['visuals'] = $visuals;

        $content->update(['options' => $options]);

        return response()->json(['success' => true]);
    }

    public function destroy(Content $content)
    {
        Log::info("Attempting to delete batch for Content ID: " . $content->id);

        return DB::transaction(function () use ($content) {
            // 1. Find all child social posts (check both string and int just in case)
            $childPosts = Content::where('options->original_content_id', $content->id)
                ->orWhere('options->original_content_id', (string)$content->id)
                ->get();

            Log::info("Found " . $childPosts->count() . " child posts to remove.");

            foreach ($childPosts as $post) {
                $options = $post->options ?? [];
                $platform = $options['platform'] ?? null;
                $postId = $options['platform_post_id'] ?? null;

                // 2. If it's on Facebook and we have a post ID, try to delete it
                if ($platform === 'facebook' && $postId) {
                    Log::info("Attempting live removal from Facebook: $postId");
                    $this->removeFromFacebook($post);
                }

                // 3. Delete the post record
                $post->delete();
            }

            // 4. Delete the parent content (the batch itself)
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Batch and associated social posts removed successfully.'
            ]);
        });
    }

    /**
     * Remove content from Facebook.
     * 
     * @deprecated Use SocialPublishingService::removeFromFacebook() directly.
     */
    private function removeFromFacebook(Content $content): void
    {
        $this->socialPublishingService->removeFromFacebook($content);
    }

    /**
     * Clean markdown for social media platforms.
     * 
     * @deprecated Use SocialPublishingService::cleanMarkdownForSocial() directly.
     */
    private function cleanMarkdownForSocial(string $text): string
    {
        return $this->socialPublishingService->cleanMarkdownForSocial($text);
    }
}