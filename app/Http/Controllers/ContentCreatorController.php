<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\KnowledgeBaseAsset;
use App\Models\MediaAsset;
use App\Services\ContentService;
use App\Services\TokenService;
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
        protected TokenService $tokenService
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

    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'type' => 'required|string',
            'count' => 'nullable|integer|min:1',
            'tone' => 'nullable|string',
            'length' => 'nullable|string',
            'context' => 'nullable|string',
            'cta' => 'nullable|string',
            'addLineBreaks' => 'nullable|boolean',
            'includeHashtags' => 'nullable|boolean',
            'generator' => 'nullable|string',
            'brand_id' => 'nullable|uuid',
            
            // Video Params
            'video_platform' => 'nullable|string',
            'video_hook' => 'nullable|string',
            'video_duration' => 'nullable|string',
            'video_style' => 'nullable|string',
            'video_description' => 'nullable|string',
            'source_image' => 'nullable|string',
            'ai_model' => 'nullable|string',
            'resolution' => 'nullable|string',
            'aspect_ratio' => 'nullable|string',
            'generation_duration' => 'nullable|string',

            // Blog Params
            'blog_keywords' => 'nullable|string',
            'blog_structure' => 'nullable|string',
            'is_batch_mode' => 'nullable|boolean',
            'featured_image_type' => 'nullable|string',
        ]);

        $count = $request->input('count', 1);
        $tokenCost = $count * 10; // 10 tokens per post

        // 1. Check & Consume Tokens
        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'content_generation', ['topic' => $request->topic])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This request requires $tokenCost tokens."
            ], 402);
        }

        $options = $request->only([
            'count', 'tone', 'length', 'cta', 'addLineBreaks', 'includeHashtags',
            'generator', 'video_platform', 'video_hook', 'video_duration', 'video_style', 
            'video_description', 'source_image', 'ai_model', 'resolution', 'aspect_ratio', 
            'generation_duration', 'blog_keywords', 'blog_structure', 'is_batch_mode', 'featured_image_type',
            'brand_id'
        ]);

        // Inject Brand Context
        $context = $request->input('context');
        if ($request->filled('brand_id')) {
            $brand = \App\Models\Brand::find($request->brand_id);
            if ($brand) {
                $brandContext = "\n\n[SYSTEM: STRICT BRAND GUIDELINES ENFORCED]\n";
                $brandContext .= "Identity: {$brand->name}\n";
                if (!empty($brand->voice_profile['tone'])) $brandContext .= "Tone of Voice: {$brand->voice_profile['tone']}\n";
                if (!empty($brand->voice_profile['keywords'])) $brandContext .= "Mandatory Keywords: {$brand->voice_profile['keywords']}\n";
                if (!empty($brand->contact_info['website'])) $brandContext .= "Website Context: {$brand->contact_info['website']}\n";
                $context .= $brandContext;
            }
        }

        $content = Content::create([
            'title' => $request->input('topic'), // Default title to topic
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
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,mov,avi,wmv|max:51200', // 50MB max, images & videos
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
            $newText = $this->contentService->generateText(
                $content->topic, 
                $content->type, 
                "REWRITE THIS POST. Original context: " . $content->context . ". \n\nCONTENT TO IMPROVE: " . $request->current_text,
                $content->options ?? []
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

    private function postToFacebook(Content $content)
    {
        $options = $content->options;
        $pageId = $options['page_id'] ?? null;
        $token = $options['page_token'] ?? null;

        if (!$pageId || !$token) {
            return ['success' => false, 'error' => 'Missing Page ID or Access Token'];
        }

        $message = $this->cleanMarkdownForSocial($content->result);
        $imageUrl = $options['image_url'] ?? null;

        try {
            if ($imageUrl) {
                if ($this->isVideo($imageUrl)) {
                    // Video Post
                    $response = Http::post("https://graph-video.facebook.com/v18.0/$pageId/videos", [
                        'file_url' => $imageUrl,
                        'description' => $message,
                        'access_token' => $token
                    ]);
                } else {
                    // Photo Post
                    $response = Http::post("https://graph.facebook.com/v18.0/$pageId/photos", [
                        'url' => $imageUrl,
                        'message' => $message,
                        'access_token' => $token
                    ]);
                }
            } else {
                // Text Post
                $response = Http::post("https://graph.facebook.com/v18.0/$pageId/feed", [
                    'message' => $message,
                    'access_token' => $token
                ]);
            }

            $data = $response->json();

            if (isset($data['id'])) {
                return ['success' => true, 'id' => $data['id']];
            } else {
                Log::error("FB Post Error: " . json_encode($data));
                return ['success' => false, 'error' => $data['error']['message'] ?? 'Unknown error'];
            }
        } catch (\Exception $e) {
            Log::error("FB Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function postToInstagram(Content $content, string $igUserId)
    {
        $options = $content->options;
        $token = $options['page_token'] ?? null;
        $imageUrl = $options['image_url'] ?? null;
        $caption = $this->cleanMarkdownForSocial($content->result);

        if (!$token || !$imageUrl) {
            return ['success' => false, 'error' => 'Instagram requires an image/video and a valid token.'];
        }

        if (str_starts_with($imageUrl, '/')) {
            $imageUrl = rtrim(config('app.url'), '/') . $imageUrl;
        }

        Log::info("Posting to IG ($igUserId) with Media: $imageUrl");

        try {
            $params = [
                'caption' => $caption,
                'access_token' => $token
            ];

            if ($this->isVideo($imageUrl)) {
                $params['media_type'] = 'VIDEO';
                $params['video_url'] = $imageUrl;
            } else {
                $params['image_url'] = $imageUrl;
            }

            $response = Http::post("https://graph.facebook.com/v18.0/$igUserId/media", $params);
            
            $containerData = $response->json();
            Log::info("IG Container Response: " . json_encode($containerData));
            
            if (!isset($containerData['id'])) {
                return ['success' => false, 'error' => 'Container Create Failed: ' . ($containerData['error']['message'] ?? json_encode($containerData))];
            }
            
            $creationId = $containerData['id'];
            
            // Wait for processing if it's a video
            if ($this->isVideo($imageUrl)) {
                sleep(10); // Videos take longer to process
            } else {
                sleep(5);
            }

            $publishResponse = Http::post("https://graph.facebook.com/v18.0/$igUserId/media_publish", [
                'creation_id' => $creationId,
                'access_token' => $token
            ]);
            
            $publishData = $publishResponse->json();
            Log::info("IG Publish Response: " . json_encode($publishData));

            if (isset($publishData['id'])) {
                return ['success' => true, 'id' => $publishData['id']];
            } else {
                return ['success' => false, 'error' => 'Publish Failed: ' . ($publishData['error']['message'] ?? json_encode($publishData))];
            }

        } catch (\Exception $e) {
            Log::error("IG Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function isVideo(string $url): bool
    {
        return preg_match('/\.(mp4|mov|avi|wmv|webm)$/i', $url) === 1;
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

    private function removeFromFacebook(Content $content)
    {
        $options = $content->options;
        $postId = $options['platform_post_id'] ?? null;
        $token = $options['page_token'] ?? null;

        if (!$postId || !$token) {
            Log::warning("Skipping Facebook removal for post ID: {" . $content->id . "} due to missing data.");
            return;
        }

        try {
            // Facebook Delete API: DELETE /{post-id}?access_token={token}
            $response = Http::delete("https://graph.facebook.com/v18.0/$postId", [
                'access_token' => $token
            ]);
            
            if ($response->successful()) {
                Log::info("Successfully deleted Facebook Post: $postId");
            } else {
                Log::error("Facebook Delete API Error (ID $postId): " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception deleting Facebook post $postId: " . $e->getMessage());
        }
    }

    private function cleanMarkdownForSocial(string $text): string
    {
        // Remove bold/italic markers
        $text = str_replace(['**', '*', '__', '_'], '', $text);
        
        // Remove markdown headers (e.g., # Header or ## Header)
        $text = preg_replace('/^#+\s+/m', '', $text);
        
        // Remove code block markers
        $text = str_replace('```', '', $text);
        $text = preg_replace('/`(.+?)`/', '$1', $text);
        
        // Remove trailing or leading whitespace
        $text = trim($text);
        
        return $text;
    }
}