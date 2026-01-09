<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Services\ResearchService;
use App\Services\TokenService;
use App\Notifications\IntelligenceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResearchEngineController extends Controller
{
    public function __construct(
        private readonly ResearchService $researchService,
        protected TokenService $tokenService
    ) {}

    public function index()
    {
        $stats = [
            'total_reports' => Research::where('status', 'completed')->count(),
            'active_research' => Research::where('status', 'researching')->count(),
            'sources_analyzed' => Research::sum('sources_count'),
            'success_rate' => Research::count() > 0 
                ? round((Research::where('status', 'completed')->count() / Research::count()) * 100, 1) 
                : 100,
        ];

        $recentResearches = Research::latest()->take(10)->get();

        return view('research-engine.research-engine', compact('stats', 'recentResearches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'query' => 'required|string',
        ]);

        $tokenCost = 50;

        // 1. Check & Consume Tokens
        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'deep_research', ['query' => $request->query])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. Research reports require $tokenCost tokens."
            ], 402);
        }

        $research = Research::create([
            'title' => $request->input('title'),
            'query' => $request->input('query'),
            'status' => 'researching',
        ]);

        // Dispatch Job
        \App\Jobs\PerformResearch::dispatch($research, auth()->user(), $tokenCost);

        // Notify user immediately that protocol started
        return response()->json([
            'success' => true,
            'message' => 'Research protocol initialized. Agents deployed.',
            'research' => $research,
            'redirect' => route('research-engine.show', $research->id)
        ]);
    }

    public function show(Research $research)
    {
        return view('research-engine.research-report', compact('research'));
    }

    public function destroy(Research $research)
    {
        $research->delete();
        return response()->json(['success' => true]);
    }
}