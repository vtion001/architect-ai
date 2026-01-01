<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Content;

class SocialPlannerController extends Controller
{
    public function __construct(
        protected \App\Services\ResearchService $researchService
    ) {}

    public function index()
    {
        $scheduledPosts = Content::where('type', 'social-post')
            ->where('status', 'scheduled')
            ->latest()
            ->get();

        return view('social-planner.index', compact('scheduledPosts'));
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
