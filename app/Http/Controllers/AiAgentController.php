<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiAgent;
use App\Models\AgentConversation;
use App\Models\KnowledgeBaseAsset;
use App\Services\AuthorizationService;
use App\Services\ResearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\ProcessAiChatMessage;

class AiAgentController extends Controller
{
    public function __construct(
        protected AuthorizationService $authService,
        protected ResearchService $researchService
    ) {}

    public function index()
    {
        $this->authService->audit(auth()->user(), 'ai_agents.view');
        
        $agents = AiAgent::where('tenant_id', auth()->user()->tenant_id)->latest()->get();
        $knowledgeAssets = KnowledgeBaseAsset::where('tenant_id', auth()->user()->tenant_id)
            ->select('id', 'title', 'category', 'type')
            ->get();
        
        $brands = auth()->user()->tenant->brands()->get();

        return view('ai-agents.index', compact('agents', 'knowledgeAssets', 'brands'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'goal' => 'required|string',
            'backstory' => 'nullable|string',
            'knowledge_sources' => 'nullable|array',
            'knowledge_sources.*' => 'exists:knowledge_base_assets,id',
            // Appearance
            'avatar_url' => 'nullable|url',
            'primary_color' => 'nullable|string|max:7',
            'welcome_message' => 'nullable|string|max:500',
            // Behavior
            'model' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:100|max:8000',
            'system_prompt' => 'nullable|string',
            // Widget
            'widget_position' => 'nullable|in:bottom-right,bottom-left,top-right,top-left',
            'widget_enabled' => 'nullable|boolean',
        ]);

        $agent = AiAgent::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return response()->json(['success' => true, 'agent' => $agent]);
    }

    public function update(Request $request, AiAgent $agent): JsonResponse
    {
        // Policy-based authorization
        $this->authorize('update', $agent);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|max:255',
            'goal' => 'sometimes|string',
            'backstory' => 'nullable|string',
            'knowledge_sources' => 'nullable|array',
            'avatar_url' => 'nullable|url',
            'primary_color' => 'nullable|string|max:7',
            'welcome_message' => 'nullable|string|max:500',
            'model' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:100|max:8000',
            'system_prompt' => 'nullable|string',
            'widget_position' => 'nullable|in:bottom-right,bottom-left,top-right,top-left',
            'widget_enabled' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $agent->update($validated);

        return response()->json(['success' => true, 'agent' => $agent->fresh()]);
    }

    public function show(AiAgent $agent): JsonResponse
    {
        $this->authorize('view', $agent);

        return response()->json(['success' => true, 'agent' => $agent]);
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:ai_agents,id',
            'message' => 'nullable|string|max:5000',
            'session_id' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'mode' => 'nullable|in:quick,thinking',
            'image' => 'nullable|image|max:5120', // 5MB max
        ]);

        $agent = AiAgent::findOrFail($validated['agent_id']);
        
        // Policy-based authorization
        $this->authorize('chat', $agent);

        // Handle Image Upload
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'chat-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/chat-images'), $filename);
            $imageUrl = asset('uploads/chat-images/' . $filename);
        }

        if (empty($validated['message']) && !$imageUrl) {
            return response()->json(['success' => false, 'message' => 'Message or image required'], 422);
        }

        // Get or create conversation
        $sessionId = $validated['session_id'] ?? Str::uuid()->toString();
        $conversation = AgentConversation::firstOrCreate(
            ['agent_id' => $agent->id, 'session_id' => $sessionId],
            ['tenant_id' => $agent->tenant_id, 'user_id' => auth()->id(), 'messages' => []]
        );

        // Add user message with metadata
        $conversation->addMessage('user', $validated['message'] ?? '', ['image_url' => $imageUrl]);

        // Dispatch Job
        ProcessAiChatMessage::dispatch(
            auth()->user(),
            $agent,
            $conversation,
            $validated['message'] ?? '',
            $validated['brand_id'] ?? null,
            $validated['mode'] ?? 'quick',
            $imageUrl
        );

        return response()->json([
            'success' => true,
            'message' => 'Message processing',
            'status' => 'processing',
            'session_id' => $sessionId,
            'conversation_id' => $conversation->id,
        ]);
    }

    private function sanitizeAgentResponse(string $text): string
    {
        // Remove markdown bold/italic/header symbols
        $text = str_replace(['**', '##', '#'], '', $text);
        
        // Final fallback: remove any single * that might be lingering
        $text = str_replace('*', '', $text);

        return trim($text);
    }

    public function getConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:ai_agents,id',
            'session_id' => 'required|string',
        ]);

        $conversation = AgentConversation::where('agent_id', $validated['agent_id'])
            ->where('session_id', $validated['session_id'])
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => true,
                'messages' => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => $conversation->messages ?? [],
        ]);
    }

    public function clearConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:ai_agents,id',
            'session_id' => 'required|string',
        ]);

        AgentConversation::where('agent_id', $validated['agent_id'])
            ->where('session_id', $validated['session_id'])
            ->delete();

        return response()->json(['success' => true]);
    }

    public function destroy(AiAgent $agent): JsonResponse
    {
        if ($agent->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Delete related conversations
        $agent->conversations()->delete();
        $agent->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get agent widget data (public endpoint for embedded widgets)
     */
    public function widget(AiAgent $agent): JsonResponse
    {
        if (!$agent->is_active || !$agent->widget_enabled) {
            return response()->json(['success' => false, 'message' => 'Widget not available'], 404);
        }

        return response()->json([
            'success' => true,
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'role' => $agent->role,
                'avatar_url' => $agent->avatar_url,
                'primary_color' => $agent->primary_color,
                'welcome_message' => $agent->welcome_message,
                'widget_position' => $agent->widget_position,
            ],
        ]);
    }
}