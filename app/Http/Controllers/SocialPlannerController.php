<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialPlannerController extends Controller
{
    public function __construct(
        protected \App\Services\ResearchService $researchService
    ) {}

    public function index()
    {
        return view('social-planner.index');
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
