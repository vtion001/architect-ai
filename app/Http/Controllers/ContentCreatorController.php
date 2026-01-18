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
        // 1. Calculate Dynamic Cost
        $tokenCost = $this->calculateTokenCost($request);

        // 2. Check & Consume Tokens
        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'content_generation', ['topic' => $request->topic])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This request requires $tokenCost tokens."
            ], 402);
        }

        $options = $request->getOptions();
        $options['count'] = (int) ($options['count'] ?? 1);

        // 3. Build Context (RAG + Brand)
        $context = $request->input('context');
        if ($request->filled('brand_id')) {
            $brandContext = $this->brandResolverService->buildBrandContext($request->brand_id);
            if ($brandContext) {
                $context .= "\n\nBRAND GUIDELINES:\n" . $brandContext;
                // Pass brand tone specifically for framework generator prompt
                $brand = \App\Models\Brand::find($request->brand_id);
                if ($brand) {
                    $options['brand_tone'] = $brand->voice_tone;
                }
            }
        }

        // 4. Handle Content Generation
        // Special Case: Framework Calendar (Synchronous Requirement for Sidebar UI)
        if ($request->input('generator') === 'framework') {
            try {
                $generatedJson = $this->contentService->generateText(
                    $request->input('topic'),
                    'framework_calendar',
                    $context,
                    $options
                );

                $content = Content::create([
                    'title' => 'Weekly Calendar: ' . Str::limit($request->input('topic'), 30),
                    'topic' => $request->input('topic'),
                    'type' => 'framework_calendar',
                    'context' => $context,
                    'status' => 'draft', // Not published yet
                    'result' => $generatedJson, // Store JSON directly
                    'options' => $options,
                ]);

                // Create individual post drafts from the JSON
                $this->createCalendarDrafts($content, $generatedJson);

                // Return with content payload for immediate UI update
                return response()->json([
                    'success' => true,
                    'content' => [
                        'id' => $content->id,
                        'content' => $generatedJson // Ensure frontend gets the JSON
                    ],
                    'message' => 'Calendar framework generated successfully.'
                ]);

            } catch (\Exception $e) {
                Log::error("Framework Generation Failed: " . $e->getMessage());
                // Refund tokens
                $this->tokenService->grant(auth()->user()->tenant, $tokenCost, 'refund_failed_generation');
                return response()->json(['success' => false, 'message' => 'Generation failed. Tokens refunded.'], 500);
            }
        }

        // Standard Case: Async Job (Post, Blog, Video)
        $content = Content::create([
            'title' => $request->input('topic'),
            'topic' => $request->input('topic'),
            'type' => $request->input('type'),
            'context' => $context,
            'status' => 'generating',
            'options' => $options,
        ]);

        \App\Jobs\GenerateContent::dispatch($content, auth()->user(), $tokenCost);

        return response()->json([
            'success' => true,
            'content' => $content,
            'message' => 'Content generation protocol initiated.'
        ]);
    }

    /**
     * Calculate token cost based on generator type.
     */
    protected function calculateTokenCost(Request $request): int
    {
        $generator = $request->input('generator');
        
        return match($generator) {
            'framework' => 50, // High value strategic asset
            'blog' => 20,      // Long form content
            'video' => str_contains($request->input('video_duration', ''), '15') ? 10 : 7,
            default => ($request->input('count', 1) * 10), // 10 per social post
        };
    }

    /**
     * Parse calendar JSON and create child content drafts.
     */
    protected function createCalendarDrafts(Content $parent, string $json): void
    {
        $data = json_decode($json, true);
        if (!$data) return;

        $pillars = ['educational', 'showcase', 'conversational', 'promotional'];
        
        foreach ($pillars as $pillar) {
            if (!isset($data[$pillar]) || !is_array($data[$pillar])) continue;

            foreach ($data[$pillar] as $post) {
                Content::create([
                    'title' => ucfirst($pillar) . ': ' . Str::limit($post['hook'] ?? 'Untitled', 30),
                    'topic' => $parent->topic,
                    'type' => 'social-post',
                    'status' => 'draft',
                    'context' => "Derived from Weekly Framework. Pillar: $pillar",
                    'result' => ($post['hook'] ?? '') . "\n\n" . ($post['caption'] ?? ''),
                    'options' => [
                        'original_content_id' => $parent->id,
                        'visual_idea' => $post['visual_idea'] ?? null,
                        'pillar' => $pillar
                    ]
                ]);
            }
        }
    }

    // ... [Rest of the methods: show, getSuggestions, refineContext, uploadMedia, generateMedia, regenerate, publish, destroy, etc.] ...
    // Note: I will retain the existing methods below to ensure full functionality.

    public function show(Content $content)
    {
        $path = storage_path('app/social_tokens.json');
        $isFacebookConnected = false;
        if (file_exists($path)) {
            $tokens = json_decode(file_get_contents($path), true);
            $isFacebookConnected = !empty($tokens['facebook']);
        }

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
        
        $brands = auth()->user()->tenant->brands()->select('id', 'name', 'colors')->get();
        
        return view('content-creator.content-viewer', compact('content', 'isFacebookConnected', 'publishedIndexes', 'brands'));
    }

    public function getSuggestions(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|min:3',
        ]);

        $suggestions = $this->researchService->suggestSocialMediaTopics($request->topic);
        
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
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,mov,avi,wmv|max:102400',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');

            if ($cloudName && $apiKey && $apiSecret) {
                try {
                    $timestamp = time();
                    $params = ['timestamp' => $timestamp];
                    ksort($params);
                    $signParts = [];
                    foreach ($params as $key => $value) {
                        $signParts[] = "$key=$value";
                    }
                    $signString = implode('&', $signParts) . $apiSecret;
                    $signature = sha1($signString);

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
                        return response()->json(['success' => true, 'url' => $url]);
                    } else {
                        Log::error("Cloudinary upload failed: " . $response->status());
                    }
                } catch (\Exception $e) {
                    Log::error("Cloudinary exception: " . $e->getMessage());
                }
            }

            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/content-media'), $filename);
            $url = '/uploads/content-media/' . $filename;
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'message' => 'Uploaded locally.'
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

        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'image_generation', ['prompt' => $request->prompt])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens."
            ], 402);
        }

        $format = $request->input('format', 'realistic');
        $options = [];

        if ($format === 'poster') {
            $options['poster_text'] = $request->input('poster_text');
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

        $generatedUrl = $this->contentService->generateImage($request->prompt, $format, $options);

        if ($generatedUrl) {
            $cloudinaryService = app(\App\Services\CloudinaryService::class);
            $uploadResult = $cloudinaryService->uploadFromUrl($generatedUrl, 'ai-generated', 'uploads/content-media');
            
            $finalUrl = $uploadResult['url'];
            $source = match($uploadResult['source']) {
                'cloudinary' => 'ai_generation',
                'local' => 'ai_generation_local',
                default => 'ai_generation_temp',
            };

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
                ]
            ]);

            return response()->json([
                'success' => true,
                'url' => $finalUrl
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Image generation failed.'], 500);
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
            $options['count'] = 1; 

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
            'image_url' => 'nullable|string',
            'platforms' => 'required|array|min:1',
            'scheduled_at' => 'required|string',
            'facebook_page_id' => 'nullable|string',
            'facebook_page_token' => 'nullable|string',
            'instagram_account_id' => 'nullable|string',
        ]);

        if (!empty($validated['image_url']) && str_starts_with($validated['image_url'], '/')) {
            $validated['image_url'] = rtrim(config('app.url'), '/') . $validated['image_url'];
        }

        $scheduledAt = $validated['scheduled_at'] === 'now' ? now()->toDateTimeString() : $validated['scheduled_at'];
        
        $parentContent = Content::find($validated['content_id']);
        $results = [];
        $isImmediate = $validated['scheduled_at'] === 'now';

        $totalPlatforms = count($validated['platforms']);
        $totalTokenCost = $totalPlatforms * 5;

        if (!$this->tokenService->consume(auth()->user(), $totalTokenCost, 'social_deployment', ['content_id' => $validated['content_id']])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens."
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
                    $fbResult = $this->socialPublishingService->postToFacebook($contentRecord);
                    $results['facebook'] = $fbResult;
                    
                    if ($fbResult['success']) {
                        $currentOptions = $contentRecord->options;
                        $currentOptions['platform_post_id'] = $fbResult['id'];
                        $contentRecord->update([
                            'status' => 'published', 
                            'result' => $validated['final_text'] . "\n\n[Posted to Facebook: " . ($fbResult['id'] ?? 'Success') . "]",
                            'options' => $currentOptions
                        ]);
                    }
                }

                if ($platform === 'instagram' && !empty($options['instagram_id']) && !empty($options['page_token'])) {
                    $igResult = $this->socialPublishingService->postToInstagram($contentRecord, $options['instagram_id']);
                    $results['instagram'] = $igResult;

                    if ($igResult['success']) {
                        $currentOptions = $contentRecord->options;
                        $currentOptions['platform_post_id'] = $igResult['id'];
                        $contentRecord->update([
                            'status' => 'published',
                            'result' => $validated['final_text'] . "\n\n[Posted to Instagram: " . ($igResult['id'] ?? 'Success') . "]",
                            'options' => $currentOptions
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

    public function saveVisual(Request $request, Content $content)
    {
        $validated = $request->validate([
            'image_url' => 'required|string',
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
        return DB::transaction(function () use ($content) {
            $childPosts = Content::where('options->original_content_id', $content->id)
                ->orWhere('options->original_content_id', (string)$content->id)
                ->get();

            foreach ($childPosts as $post) {
                $options = $post->options ?? [];
                $platform = $options['platform'] ?? null;
                $postId = $options['platform_post_id'] ?? null;

                if ($platform === 'facebook' && $postId) {
                    $this->socialPublishingService->removeFromFacebook($post);
                }
                $post->delete();
            }
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Batch removed.'
            ]);
        });
    }
}
