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

        return view('content-creator.content-creator', compact('stats', 'recentContents'));
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
            'generation_duration', 'blog_keywords', 'blog_structure', 'is_batch_mode', 'featured_image_type'
        ]);

        $content = Content::create([
            'title' => $request->input('topic'), // Default title to topic
            'topic' => $request->input('topic'),
            'type' => $request->input('type'),
            'context' => $request->input('context'),
            'status' => 'generating',
            'options' => $options,
        ]);

        try {
            $generatedText = $this->contentService->generateText(
                $request->input('topic'),
                $request->input('type'),
                $request->input('context'),
                $options
            );

            // Extract a title from the first line or use topic
            $lines = explode("\n", trim($generatedText));
            $title = !empty($lines[0]) ? str_replace(['#', '*', '='], '', $lines[0]) : $request->input('topic');
            if (strlen($title) > 100) $title = substr($title, 0, 97) . '...';

            $wordCount = str_word_count(strip_tags($generatedText));

            $content->update([
                'title' => $title,
                'result' => $generatedText,
                'word_count' => $wordCount,
                'status' => 'published',
            ]);

            return response()->json([
                'success' => true,
                'content' => $content
            ]);
        } catch (\Throwable $e) {
            Log::error("Content generation failed: " . $e->getMessage());
            
            // Refund tokens on failure
            $this->tokenService->grant(auth()->user()->tenant, $tokenCost, 'refund_failed_generation');
            
            $content->update(['status' => 'failed']);
            return response()->json([
                'success' => false, 
                'message' => 'AI generation failed. Tokens refunded.'
            ], 500);
        }
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
        
        return view('content-creator.content-viewer', compact('content', 'isFacebookConnected', 'publishedIndexes'));
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
        ]);

        $tokenCost = 5;

        // 1. Check & Consume Tokens
        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'image_generation', ['prompt' => $request->prompt])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. Image generation requires $tokenCost tokens."
            ], 402);
        }

        $generatedUrl = $this->contentService->generateImage($request->prompt);

        if ($generatedUrl) {
            $finalUrl = $generatedUrl;
            $source = 'ai_generation_temp';

            // 2. Attempt Persistence (Cloudinary first, then Local)
            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');
            $uploadedToCloud = false;

            if ($cloudName && $apiKey && $apiSecret) {
                try {
                    $timestamp = time();
                    $signString = "timestamp={$timestamp}{$apiSecret}";
                    $signature = sha1($signString);

                    Log::info("Attempting Cloudinary upload for AI image...", [
                        'source_url' => Str::limit($generatedUrl, 100),
                        'timestamp' => $timestamp
                    ]);

                    $response = Http::timeout(30)->asForm()->post("https://api.cloudinary.com/v1_1/$cloudName/image/upload", [
                        'file' => $generatedUrl,
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                         $finalUrl = $response->json()['secure_url'];
                         $uploadedToCloud = true;
                         $source = 'ai_generation';
                         Log::info("Generated AI image saved to Cloudinary successfully: $finalUrl");
                    } else {
                        Log::error("Failed to save AI image to Cloudinary", [
                            'status' => $response->status(),
                            'body' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning("Cloudinary upload exception: " . $e->getMessage());
                }
            }

            // 3. Local Fallback if Cloudinary failed
            if (!$uploadedToCloud) {
                try {
                    $imageContent = file_get_contents($generatedUrl);
                    if ($imageContent !== false) {
                        $filename = 'ai_' . Str::random(40) . '.png';
                        $path = public_path('uploads/content-media');
                        
                        if (!file_exists($path)) {
                            mkdir($path, 0755, true);
                        }
                        
                        file_put_contents($path . '/' . $filename, $imageContent);
                        $finalUrl = '/uploads/content-media/' . $filename;
                        $source = 'ai_generation_local';
                        Log::info("Generated AI image saved locally: $finalUrl");
                    } else {
                        Log::error("Failed to download AI image for local fallback.");
                    }
                } catch (\Exception $e) {
                    Log::error("Local fallback exception: " . $e->getMessage());
                }
            }

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
                    'timestamp' => now()->toIso8601String()
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
                $response = Http::post("https://graph.facebook.com/v18.0/$pageId/photos", [
                    'url' => $imageUrl,
                    'message' => $message,
                    'access_token' => $token
                ]);
            } else {
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
            return ['success' => false, 'error' => 'Instagram requires an image and a valid token.'];
        }

        if (str_starts_with($imageUrl, '/')) {
            $imageUrl = rtrim(config('app.url'), '/') . $imageUrl;
        }

        Log::info("Posting to IG ($igUserId) with Image: $imageUrl");

        try {
            $response = Http::post("https://graph.facebook.com/v18.0/$igUserId/media", [
                'image_url' => $imageUrl,
                'caption' => $caption,
                'access_token' => $token
            ]);
            
            $containerData = $response->json();
            Log::info("IG Container Response: " . json_encode($containerData));
            
            if (!isset($containerData['id'])) {
                return ['success' => false, 'error' => 'Container Create Failed: ' . ($containerData['error']['message'] ?? json_encode($containerData))];
            }
            
            $creationId = $containerData['id'];
            sleep(5);

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