<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Services\ResearchService;
use App\Services\TokenService;
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
        // ... (rest of index method remains same)
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

        try {
            // ... (rest of store method remains same)
        } catch (\Throwable $e) {
            Log::error("Research failed: " . $e->getMessage());
            
            // Refund tokens on failure
            $this->tokenService->grant(auth()->user()->tenant, $tokenCost, 'refund_failed_research');
            
            $research->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Research failed. Tokens refunded.'], 500);
        }
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
