<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ContentCreatorController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        protected \App\Services\ResearchService $researchService
    ) {}
    public function index()
    {
        $stats = [
            'total_content' => Content::count(),
            'this_month' => Content::whereMonth('created_at', now()->month)->count(),
            'in_draft' => Content::where('status', 'draft')->count(),
            'published' => Content::where('status', 'published')->count(),
        ];

        $recentContents = Content::latest()->take(10)->get();

        return view('content-creator.index', compact('stats', 'recentContents'));
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
            $content->update(['status' => 'failed']);
            return response()->json([
                'success' => false, 
                'message' => 'AI generation failed. Please check logs.'
            ], 500);
        }
    }

    public function show(Content $content)
    {
        return view('content-creator.show', compact('content'));
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
            'final_text' => 'required|string',
            'image_url' => 'nullable|url',
            'platforms' => 'required|array|min:1',
            'scheduled_at' => 'required|date',
            'facebook_page_id' => 'nullable|string',
            'facebook_page_token' => 'nullable|string',
        ]);

        $parentDrag = Content::find($validated['content_id']);
        $results = [];

        foreach ($validated['platforms'] as $platform) {
            $options = [
                'platform' => $platform,
                'scheduled_at' => $validated['scheduled_at'],
                'image_url' => $validated['image_url'],
                'original_content_id' => $validated['content_id']
            ];

            // Attach specific credentials for Facebook
            if ($platform === 'facebook') {
                $options['page_id'] = $validated['facebook_page_id'] ?? null;
                $options['page_token'] = $validated['facebook_page_token'] ?? null;
            }

            $contentRecord = Content::create([
                'title' => 'Scheduled ' . ucfirst($platform) . ' - ' . Str::limit($parentDrag->topic, 20),
                'topic' => $parentDrag->topic,
                'type' => 'social-post', 
                'context' => $parentDrag->context,
                'status' => 'scheduled',
                'result' => $validated['final_text'], 
                'options' => $options
            ]);

            // If scheduled time is substantially "now" (within 2 mins), post immediately for demo purposes
            // Or if user specifically requested this to be "setup end to end", immediate feedback is good.
            if ($platform === 'facebook' && $options['page_id'] && $options['page_token']) {
                $scheduledTime = \Carbon\Carbon::parse($validated['scheduled_at']);
                if ($scheduledTime->diffInMinutes(now()) < 5 || $scheduledTime->isPast()) {
                    $fbResult = $this->postToFacebook($contentRecord);
                    $results['facebook'] = $fbResult;
                    if ($fbResult['success']) {
                        $contentRecord->update(['status' => 'published', 'result' => $validated['final_text'] . "\n\n[Posted to FB ID: {$fbResult['id']}]"]);
                    } else {
                        $contentRecord->update(['status' => 'failed', 'result' => $validated['final_text'] . "\n\n[FB Error: {$fbResult['error']}]"]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Content scheduled.',
            'debug_results' => $results
        ]);
    }

    private function postToFacebook(Content $content)
    {
        $options = $content->options;
        $pageId = $options['page_id'];
        $token = $options['page_token'];
        $message = $content->result;
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
}
