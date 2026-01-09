<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiAgent;
use App\Models\AgentConversation;
use App\Models\KnowledgeBaseAsset;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiAgentController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function index()
    {
        $this->authService->audit(auth()->user(), 'ai_agents.view');
        
        $agents = AiAgent::where('tenant_id', auth()->user()->tenant_id)->latest()->get();
        $knowledgeAssets = KnowledgeBaseAsset::where('tenant_id', auth()->user()->tenant_id)
            ->select('id', 'title', 'category', 'type')
            ->get();

        return view('ai-agents.index', compact('agents', 'knowledgeAssets'));
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
        // Ensure agent belongs to user's tenant
        if ($agent->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
        if ($agent->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json(['success' => true, 'agent' => $agent]);
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:ai_agents,id',
            'message' => 'required|string|max:5000',
            'session_id' => 'nullable|string',
        ]);

        $agent = AiAgent::findOrFail($validated['agent_id']);
        
        // Verify tenant access
        if ($agent->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Get or create conversation
        $sessionId = $validated['session_id'] ?? Str::uuid()->toString();
        $conversation = AgentConversation::firstOrCreate(
            ['agent_id' => $agent->id, 'session_id' => $sessionId],
            ['user_id' => auth()->id(), 'messages' => []]
        );

        // Add user message
        $conversation->addMessage('user', $validated['message']);

        // Build messages for API
        $systemPrompt = $agent->getFullSystemPrompt();
        
        // Add knowledge context if available
        $knowledgeContext = $agent->getKnowledgeContext();
        if ($knowledgeContext) {
            $systemPrompt .= "\n\n--- KNOWLEDGE BASE ---\n" . $knowledgeContext;
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...$conversation->getMessagesForApi(),
        ];

        try {
            $apiKey = config('services.openai.key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI service not configured'
                ], 503);
            }

            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $agent->model ?? config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => $messages,
                    'temperature' => $agent->temperature ?? 0.7,
                    'max_tokens' => $agent->max_tokens ?? 2000,
                ]);

            if ($response->successful()) {
                $assistantMessage = $response->json('choices.0.message.content');
                
                // Add assistant response to conversation
                $conversation->addMessage('assistant', $assistantMessage);

                return response()->json([
                    'success' => true,
                    'message' => $assistantMessage,
                    'session_id' => $sessionId,
                    'conversation_id' => $conversation->id,
                ]);
            }

            Log::error('AI Agent chat error', [
                'agent_id' => $agent->id,
                'error' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get response from AI'
            ], 500);

        } catch (\Exception $e) {
            Log::error('AI Agent chat exception', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
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
