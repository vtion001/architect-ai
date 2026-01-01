<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
