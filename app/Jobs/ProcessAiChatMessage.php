<?php

namespace App\Jobs;

use App\Models\AiAgent;
use App\Models\AgentConversation;
use App\Models\Brand;
use App\Models\User;
use App\Services\ResearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessAiChatMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes

    public function __construct(
        protected User $user,
        protected AiAgent $agent,
        protected AgentConversation $conversation,
        protected string $userMessage,
        protected ?string $brandId = null,
        protected string $mode = 'quick',
        protected ?string $imageUrl = null
    ) {}

    public function handle(ResearchService $researchService): void
    {
        // Set Tenant Context
        if ($this->user->tenant) {
            app()->instance(\App\Models\Tenant::class, $this->user->tenant);
        }

        try {
            // Build messages for API
            $systemPrompt = $this->agent->getFullSystemPrompt();
            
            // Inject Brand Context
            if (!empty($this->brandId)) {
                $brand = Brand::find($this->brandId);
                if ($brand) {
                    $systemPrompt .= "\n\n[STRICT BRAND IDENTITY ACTIVE]\n";
                    $systemPrompt .= "You are representing the brand: {$brand->name}\n";
                    if ($brand->voice_profile) {
                        $voice = $brand->voice_profile;
                        $systemPrompt .= "Tone: " . ($voice['tone'] ?? 'Standard') . "\n";
                        $systemPrompt .= "Style: " . ($voice['writing_style'] ?? 'Standard') . "\n";
                        if (!empty($voice['keywords'])) $systemPrompt .= "Key Phrases: {$voice['keywords']}\n";
                        if (!empty($voice['avoid_words'])) $systemPrompt .= "Avoid Words: {$voice['avoid_words']}\n";
                    }
                    if ($brand->description) $systemPrompt .= "Context: {$brand->description}\n";
                    $systemPrompt .= "[END BRAND IDENTITY]\n";
                }
            }
            
            // Add knowledge context if available (Explicitly Linked)
            $pinnedContext = $this->agent->getKnowledgeContext();
            if ($pinnedContext) {
                $systemPrompt .= "\n\n--- PINNED KNOWLEDGE ---\n" . $pinnedContext;
            }

            // Add Dynamic RAG Context (Vector/Hybrid Search)
            try {
                $ragContext = $researchService->getKnowledgeBaseContext($this->userMessage);
                if ($ragContext) {
                    $systemPrompt .= "\n\n--- RELEVANT KNOWLEDGE (SEARCH) ---\n" . $ragContext;
                }
            } catch (\Exception $e) {
                Log::warning("Agent RAG failed: " . $e->getMessage());
            }

            // Format User Message (with Vision support if needed)
            $userContent = [];
            if ($this->imageUrl) {
                $userContent[] = ['type' => 'text', 'text' => $this->userMessage ?: 'Analyze this image.'];
                $userContent[] = ['type' => 'image_url', 'image_url' => ['url' => $this->imageUrl]];
            } else {
                $userContent = $this->userMessage;
            }

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt . "\n\nCRITICAL: DO NOT use markdown symbols like '*' or '#' for formatting. Use plain text and clear spacing. For lists, use simple bullet points like '-' or '•'"],
                ...$this->conversation->getMessagesForApi(),
            ];

            // If we just added the last message locally, replace its content with the Vision-formatted one
            if ($this->imageUrl) {
                $lastIdx = count($messages) - 1;
                if ($messages[$lastIdx]['role'] === 'user') {
                    $messages[$lastIdx]['content'] = $userContent;
                }
            }

            $apiKey = config('services.openai.key');
            if (!$apiKey) {
                Log::error('AI service not configured for job.');
                return;
            }

            // Determine Model: Thinking mode or Images require higher tier
            $model = $this->agent->model ?? config('services.openai.model', 'gpt-4o-mini');
            if ($this->mode === 'thinking' || $this->imageUrl) {
                $model = 'gpt-4o'; // Upgrade for deep thinking or vision
            }

            $response = Http::withToken($apiKey)
                ->timeout(90) // Increased for thinking/vision
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => $this->agent->temperature ?? 0.7,
                    'max_tokens' => $this->agent->max_tokens ?? 2000,
                ]);

            if ($response->successful()) {
                $assistantMessage = $response->json('choices.0.message.content');
                
                // Sanitize response
                $assistantMessage = $this->sanitizeAgentResponse($assistantMessage);
                
                // Add assistant response to conversation
                $this->conversation->addMessage('assistant', $assistantMessage);
            } else {
                Log::error('AI Agent chat job error', [
                    'agent_id' => $this->agent->id,
                    'error' => $response->body()
                ]);
                $this->conversation->addMessage('assistant', 'Sorry, I encountered an error processing your request.');
            }

        } catch (\Throwable $e) {
            Log::error('AI Agent chat job exception', [
                'agent_id' => $this->agent->id,
                'error' => $e->getMessage()
            ]);
            $this->conversation->addMessage('assistant', 'An error occurred while processing your request.');
        }
    }

    private function sanitizeAgentResponse(string $text): string
    {
        // Remove markdown bold/italic/header symbols
        $text = str_replace(['**', '##', '#'], '', $text);
        
        // Final fallback: remove any single * that might be lingering
        $text = str_replace('*', '', $text);

        return trim($text);
    }
}
