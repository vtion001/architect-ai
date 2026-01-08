<?php

namespace App\Http\Controllers;

use App\Models\AiAgent;
use App\Models\KnowledgeBaseAsset;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function index()
    {
        $this->authService->audit(auth()->user(), 'ai_agents.view');
        
        $agents = AiAgent::latest()->get();
        $knowledgeAssets = KnowledgeBaseAsset::select('id', 'title', 'category', 'type')->get();

        return view('ai-agents.index', compact('agents', 'knowledgeAssets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'goal' => 'required|string',
            'backstory' => 'nullable|string',
            'knowledge_sources' => 'nullable|array',
            'knowledge_sources.*' => 'exists:knowledge_base_assets,id',
        ]);

        $agent = AiAgent::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'role' => $request->role,
            'goal' => $request->goal,
            'backstory' => $request->backstory,
            'knowledge_sources' => $request->knowledge_sources ?? [],
        ]);

        return response()->json(['success' => true, 'agent' => $agent]);
    }

    public function destroy(AiAgent $agent)
    {
        $agent->delete();
        return response()->json(['success' => true]);
    }
}
