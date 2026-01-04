<?php

namespace App\Http\Controllers;

use App\Models\Content;
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
            // ...
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

        return response()->json([
            'suggestions' => $suggestions
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
            'file' => 'required|image|max:10240', // 10MB max
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
                    $signature = sha1("timestamp=$timestamp$apiSecret");

                    // Use attach() for file uploads (multipart/form-data)
                    $response = Http::attach(
                        'file', 
                        fopen($file->getRealPath(), 'r'), 
                        $file->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/$cloudName/image/upload", [
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                        $url = $response->json()['secure_url'];
                        Log::info("Media uploaded to Cloudinary. URL: $url");
                        return response()->json(['success' => true, 'url' => $url]);
                    } else {
                        Log::error("Cloudinary upload failed: " . $response->body());
                        return response()->json(['success' => false, 'message' => 'Cloudinary upload failed.'], 500);
                    }
                } catch (\Exception $e) {
                    Log::error("Cloudinary exception: " . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Upload error.'], 500);
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
                'message' => 'Uploaded locally. Add CLOUDINARY_ keys to .env for cloud storage.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    public function regenerate(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:contents,id',
            'current_text' => 'required|string',
        ]);

        $content = Content::findOrFail($request->content_id);
        
        try {
            // Simple regen prompt logic
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
             return response()->json(['success' => false, 'message' => 'Regeneration failed'], 500);
        }
    }

    public function publish(Request $request)
    {
        $validated = $request->validate([
            'content_id' => 'required|exists:contents,id',
            'segment_index' => 'required|integer', // Track which part of the batch this is
            'final_text' => 'required|string',
            'image_url' => 'nullable|url',
            'platforms' => 'required|array|min:1',
            'scheduled_at' => 'required|string', // Allow 'now' string or date
            'facebook_page_id' => 'nullable|string',
            'facebook_page_token' => 'nullable|string',
            'instagram_account_id' => 'nullable|string',
        ]);

        // Resolve 'now' to actual timestamp
        $scheduledAt = $validated['scheduled_at'] === 'now' ? now()->toDateTimeString() : $validated['scheduled_at'];
        
        $parentDrag = Content::find($validated['content_id']);
        $results = [];

        $isImmediate = $validated['scheduled_at'] === 'now';

        foreach ($validated['platforms'] as $platform) {
            $options = [
                'platform' => $platform,
                'scheduled_at' => $scheduledAt,
                'image_url' => $validated['image_url'],
                'original_content_id' => (int)$validated['content_id'],
                'segment_index' => (int)$validated['segment_index']
            ];

            // Attach specific credentials for Facebook
            if ($platform === 'facebook') {
                $options['page_id'] = $validated['facebook_page_id'] ?? null;
                $options['page_token'] = $validated['facebook_page_token'] ?? null;
            }
            
            // Attach credentials for Instagram (uses FB Page Token)
            if ($platform === 'instagram') {
                $options['instagram_id'] = $validated['instagram_account_id'] ?? null;
                $options['page_token'] = $validated['facebook_page_token'] ?? null;
            }

            $contentRecord = Content::create([
                'title' => ($isImmediate ? 'Published ' : 'Scheduled ') . ucfirst($platform) . ' - ' . Str::limit($parentDrag->topic, 20),
                'topic' => $parentDrag->topic,
                'type' => 'social-post', 
                'context' => $parentDrag->context,
                'status' => 'scheduled',
                'result' => $validated['final_text'], 
                'options' => $options
            ]);

            // If scheduled at 'now', or time is past, attempt immediate post
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
                // Photo Post
                $response = Http::post("https://graph.facebook.com/v18.0/$pageId/photos", [
                    'url' => $imageUrl,
                    'message' => $message,
                    'access_token' => $token
                ]);
            } else {
                // Text/Feed Post
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

        // Ensure URL is absolute for local uploads
        if (str_starts_with($imageUrl, '/')) {
            $imageUrl = rtrim(config('app.url'), '/') . $imageUrl;
        }

        Log::info("Posting to IG ($igUserId) with Image: $imageUrl");

        try {
            // 1. Create Media Container
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

            // Give IG a moment to process the image download (prevents "Media not ready" errors)
            sleep(5);

            // 2. Publish Media
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


    public function generateMedia(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:3',
        ]);

        $generatedUrl = $this->contentService->generateImage($request->prompt);

        if ($generatedUrl) {
            // Attempt to upload generated URL to Cloudinary for persistence
            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');

            if ($cloudName && $apiKey && $apiSecret) {
                try {
                    $timestamp = time();
                    $signature = sha1("timestamp=$timestamp$apiSecret");

                    // Use asForm() for URL uploads to ensure application/x-www-form-urlencoded
                    $response = Http::asForm()->post("https://api.cloudinary.com/v1_1/$cloudName/image/upload", [
                        'file' => $generatedUrl, // Cloudinary accepts remote URLs!
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                         $generatedUrl = $response->json()['secure_url'];
                         Log::info("Generated AI image saved to Cloudinary: $generatedUrl");
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to save AI image to Cloudinary: " . $e->getMessage());
                    // Silently fail back to original OpenAI URL
                }
            }

            return response()->json([
                'success' => true,
                'url' => $generatedUrl
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Image generation failed. Please try again.'], 500);
    }
    public function saveVisual(Request $request, Content $content)
    {
        $validated = $request->validate([
            'image_url' => 'required|url',
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
            Log::warning("Skipping Facebook removal for post ID: {$content->id} due to missing data.");
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
