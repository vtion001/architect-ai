<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ChatProcessingResult;
use App\Models\AiAgent;
use App\Models\AgentConversation;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Chat Processing Service
 * 
 * ISOLATED service for processing AI chat messages.
 * 
 * This service is DECOUPLED from:
 * - UI components (ai-chat-widget.blade.php)
 * - Job dispatching (ProcessAiChatMessage job)
 * - Chat head positioning and UI layout
 * 
 * Changes to this service will NOT affect:
 * - Chat widget appearance
 * - Chat head positions
 * - UI animations or transitions
 */
class AiChatProcessingService
{
    public function __construct(
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
            // Build system prompt with context
            $systemPrompt = $this->buildSystemPrompt($agent, $brandId, $userMessage);
            
            // Format messages for API
            $messages = $this->formatMessagesForApi($systemPrompt, $conversation, $userMessage, $imageUrl);
            
            // Select model based on mode and requirements
            $model = $this->selectModel($agent, $mode, $imageUrl);
            
            // Call OpenAI API
            $response = $this->callOpenAI($messages, $agent, $model);
            
            if ($response['success']) {
                $sanitizedMessage = $this->sanitizeResponse($response['message']);
                $conversation->addMessage('assistant', $sanitizedMessage);
                
                return ChatProcessingResult::success($sanitizedMessage);
            }
            
            $errorMessage = 'Sorry, I encountered an error processing your request.';
            $conversation->addMessage('assistant', $errorMessage);
            
            return ChatProcessingResult::failure($errorMessage, $response['error'] ?? 'Unknown error');
            
        } catch (\Throwable $e) {
            Log::error('AI Chat processing error', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
            
            $errorMessage = 'An error occurred while processing your request.';
            $conversation->addMessage('assistant', $errorMessage);
            
            return ChatProcessingResult::failure($errorMessage, $e->getMessage());
        }
    }

    /**
     * Build the complete system prompt with all context.
     */
    protected function buildSystemPrompt(AiAgent $agent, ?string $brandId, string $userMessage): string
    {
        $systemPrompt = $agent->getFullSystemPrompt();
        
        // Add brand context
        $systemPrompt .= $this->buildBrandContext($brandId);
        
        // Add pinned knowledge
        $pinnedContext = $agent->getKnowledgeContext();
        if ($pinnedContext) {
            $systemPrompt .= "\n\n--- PINNED KNOWLEDGE ---\n" . $pinnedContext;
        }
        
        // Add RAG context
        $systemPrompt .= $this->buildRagContext($userMessage);
        
        // Add output formatting instructions
        $systemPrompt .= "\n\nCRITICAL: DO NOT use markdown symbols like '*' or '#' for formatting. Use plain text and clear spacing. For lists, use simple bullet points like '-' or '•'";
        
        return $systemPrompt;
    }

    /**
     * Build brand context for the prompt.
     */
    protected function buildBrandContext(?string $brandId): string
    {
        if (empty($brandId)) {
            return '';
        }
        
        $brand = Brand::find($brandId);
        if (!$brand) {
            return '';
        }
        
        $context = "\n\n[STRICT BRAND IDENTITY ACTIVE]\n";
        $context .= "You are representing the brand: {$brand->name}\n";
        
        if ($brand->voice_profile) {
            $voice = $brand->voice_profile;
            $context .= "Tone: " . ($voice['tone'] ?? 'Standard') . "\n";
            $context .= "Style: " . ($voice['writing_style'] ?? 'Standard') . "\n";
            if (!empty($voice['keywords'])) {
                $context .= "Key Phrases: {$voice['keywords']}\n";
            }
            if (!empty($voice['avoid_words'])) {
                $context .= "Avoid Words: {$voice['avoid_words']}\n";
            }
        }
        
        if ($brand->description) {
            $context .= "Context: {$brand->description}\n";
        }
        
        $context .= "[END BRAND IDENTITY]\n";
        
        return $context;
    }

    /**
     * Build RAG context from knowledge base.
     */
    protected function buildRagContext(string $userMessage): string
    {
        try {
            $ragContext = $this->researchService->getKnowledgeBaseContext($userMessage);
            if ($ragContext) {
                return "\n\n--- RELEVANT KNOWLEDGE (SEARCH) ---\n" . $ragContext;
            }
        } catch (\Exception $e) {
            Log::warning("Agent RAG failed: " . $e->getMessage());
        }
        
        return '';
    }

    /**
     * Format messages for OpenAI API.
     */
    protected function formatMessagesForApi(
        string $systemPrompt,
        AgentConversation $conversation,
        string $userMessage,
        ?string $imageUrl
    ): array {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...$conversation->getMessagesForApi(),
        ];
        
        // Handle vision content
        if ($imageUrl) {
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
     * Select the appropriate model based on mode and requirements.
     */
    protected function selectModel(AiAgent $agent, string $mode, ?string $imageUrl): string
    {
        $model = $agent->model ?? config('services.openai.model', 'gpt-4o-mini');
        
        // Upgrade for deep thinking or vision
        if ($mode === 'thinking' || $imageUrl) {
            $model = 'gpt-4o';
        }
        
        return $model;
    }

    /**
     * Call OpenAI API.
     */
    protected function callOpenAI(array $messages, AiAgent $agent, string $model): array
    {
        $apiKey = config('services.openai.key');
        
        if (!$apiKey) {
            Log::error('AI service not configured');
            return ['success' => false, 'error' => 'AI service not configured'];
        }
        
        $response = Http::withToken($apiKey)
            ->timeout(90)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $agent->temperature ?? 0.7,
                'max_tokens' => $agent->max_tokens ?? 2000,
            ]);
        
        if ($response->successful()) {
            return [
                'success' => true,
                'message' => $response->json('choices.0.message.content'),
            ];
        }
        
        Log::error('AI Agent chat error', ['error' => $response->body()]);
        
        return [
            'success' => false,
            'error' => $response->body(),
        ];
    }

    /**
     * Sanitize AI response to remove markdown formatting.
     */
    protected function sanitizeResponse(string $text): string
    {
        // Remove markdown bold/italic/header symbols
        $text = str_replace(['**', '##', '#'], '', $text);
        
        // Remove any single * that might be lingering
        $text = str_replace('*', '', $text);
        
        return trim($text);
    }
}
