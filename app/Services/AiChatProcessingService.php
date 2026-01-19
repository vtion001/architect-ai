<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ChatProcessingResult;
use App\Models\AiAgent;
use App\Models\AgentConversation;
use App\Models\Brand;
use App\Models\User;
use App\Services\AI\OpenAIClient;
use App\Services\AI\PromptBuilder;
use Illuminate\Support\Facades\Log;

/**
 * AI Chat Processing Service
 * 
 * Processes AI chat messages for agent conversations.
 * 
 * ARCHITECTURE:
 * - Uses OpenAIClient for API calls
 * - Uses PromptBuilder for prompt construction
 * - Uses TokenService for token management
 * - Uses ResearchService for RAG context
 * 
 * DECOUPLED FROM:
 * - UI components (ai-chat-widget.blade.php)
 * - Job dispatching (ProcessAiChatMessage job)
 */
class AiChatProcessingService
{
    public function __construct(
        protected OpenAIClient $aiClient,
        protected PromptBuilder $promptBuilder,
        protected TokenService $tokenService,
        protected ResearchService $researchService
    ) {}

    /**
     * Process a chat message and generate AI response.
     */
    public function process(
        User $user,
        AiAgent $agent,
        AgentConversation $conversation,
        string $userMessage,
        ?string $brandId = null,
        string $mode = 'quick',
        ?string $imageUrl = null
    ): ChatProcessingResult {
        try {
            // 1. Consume tokens first
            if (!$this->consumeTokens($user, $agent->id, $mode)) {
                return $this->insufficientTokensResponse($conversation);
            }

            // 2. Build prompt and messages
            $systemPrompt = $this->buildSystemPrompt($agent, $brandId, $userMessage);
            $messages = $this->formatMessages($systemPrompt, $conversation, $userMessage, $imageUrl);
            
            // 3. Call AI
            $response = $this->aiClient->chat($messages, [
                'model' => $this->selectModel($agent, $mode, $imageUrl),
                'temperature' => $agent->temperature ?? 0.7,
                'max_tokens' => $agent->max_tokens ?? 2000,
            ]);

            // 4. Handle response
            if ($response['success']) {
                $sanitized = $this->promptBuilder->sanitize($response['message']);
                $conversation->addMessage('assistant', $sanitized);
                return ChatProcessingResult::success($sanitized);
            }

            // Refund on failure
            $this->refundTokens($user);
            return $this->errorResponse($conversation, $response['error']);

        } catch (\Throwable $e) {
            Log::error('AI Chat error', ['agent' => $agent->id, 'error' => $e->getMessage()]);
            return $this->errorResponse($conversation, $e->getMessage());
        }
    }

    /**
     * Consume tokens for the chat message.
     */
    protected function consumeTokens(User $user, string $agentId, string $mode): bool
    {
        $cost = $this->tokenService->getCost('ai_chat_message');
        return $this->tokenService->consume($user, $cost, 'ai_chat_message', [
            'agent_id' => $agentId,
            'mode' => $mode,
        ]);
    }

    /**
     * Refund tokens after failure.
     */
    protected function refundTokens(User $user): void
    {
        $cost = $this->tokenService->getCost('ai_chat_message');
        $this->tokenService->refund($user->tenant, $cost, 'ai_chat_api_failure');
    }

    /**
     * Build the complete system prompt.
     */
    protected function buildSystemPrompt(AiAgent $agent, ?string $brandId, string $userMessage): string
    {
        $brand = $brandId ? Brand::find($brandId) : null;
        
        return $this->promptBuilder->build([
            $agent->getFullSystemPrompt(),
            $this->promptBuilder->brandContext($brand),
            $this->promptBuilder->knowledgeContext($agent->getKnowledgeContext()),
            $this->getRagContext($userMessage),
            $this->promptBuilder->formattingInstructions('plain'),
        ]);
    }

    /**
     * Get RAG context from knowledge base.
     */
    protected function getRagContext(string $userMessage): string
    {
        try {
            $context = $this->researchService->getKnowledgeBaseContext($userMessage);
            return $context ? $this->promptBuilder->knowledgeContext($context, 'RELEVANT KNOWLEDGE') : '';
        } catch (\Exception $e) {
            Log::warning("RAG failed: {$e->getMessage()}");
            return '';
        }
    }

    /**
     * Format messages for API call.
     */
    protected function formatMessages(
        string $systemPrompt,
        AgentConversation $conversation,
        string $userMessage,
        ?string $imageUrl
    ): array {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...$conversation->getMessagesForApi(),
        ];

        if ($imageUrl && !empty($messages)) {
            $lastIdx = count($messages) - 1;
            if ($messages[$lastIdx]['role'] === 'user') {
                $messages[$lastIdx]['content'] = [
                    ['type' => 'text', 'text' => $userMessage ?: 'Analyze this image.'],
                    ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]],
                ];
            }
        }

        return $messages;
    }

    /**
     * Select the appropriate model.
     */
    protected function selectModel(AiAgent $agent, string $mode, ?string $imageUrl): string
    {
        if ($mode === 'thinking' || $imageUrl) {
            return 'gpt-4o';
        }
        return $agent->model ?? config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Create insufficient tokens response.
     */
    protected function insufficientTokensResponse(AgentConversation $conversation): ChatProcessingResult
    {
        $cost = $this->tokenService->getCost('ai_chat_message');
        $message = "Insufficient tokens. AI chat requires {$cost} tokens per message.";
        $conversation->addMessage('assistant', $message);
        return ChatProcessingResult::failure($message, 'insufficient_tokens');
    }

    /**
     * Create error response.
     */
    protected function errorResponse(AgentConversation $conversation, string $error): ChatProcessingResult
    {
        $message = 'Sorry, I encountered an error processing your request.';
        $conversation->addMessage('assistant', $message);
        return ChatProcessingResult::failure($message, $error);
    }
}
