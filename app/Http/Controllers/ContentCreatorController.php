<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentCreatorController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService
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
            'context' => 'nullable|string',
        ]);

        $content = Content::create([
            'title' => $request->topic, // Default title to topic
            'topic' => $request->topic,
            'type' => $request->type,
            'context' => $request->context,
            'status' => 'generating',
        ]);

        try {
            $generatedText = $this->contentService->generateText(
                $request->topic,
                $request->type,
                $request->context
            );

            // Extract a title from the first line or use topic
            $lines = explode("\n", trim($generatedText));
            $title = !empty($lines[0]) ? str_replace(['#', '*', '='], '', $lines[0]) : $request->topic;
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
        } catch (\Exception $e) {
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
}
