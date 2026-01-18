<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AiAgent;
use App\Models\AgentConversation;
use App\Models\User;
use App\Services\AiChatProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process AI Chat Message Job
 * 
 * Queue job for processing AI chat messages asynchronously.
 * 
 * ARCHITECTURE NOTE: This job is DECOUPLED from UI rendering.
 * All AI processing logic is delegated to AiChatProcessingService.
 * 
 * Changes to this job will NOT affect:
 * - Chat widget UI layout
 * - Chat head positions
 * - Message bubble styling
 * - UI animations
 * 
 * The UI components (ai-chat-widget.blade.php) are completely
 * independent and can be modified without affecting this job.
 */
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

    /**
     * Execute the job.
     * 
     * Delegates all processing to AiChatProcessingService which is
     * completely isolated from UI components.
     */
    public function handle(AiChatProcessingService $chatService): void
    {
        // Set Tenant Context for multi-tenancy
        $this->setTenantContext();

        try {
            // Delegate to isolated service
            $result = $chatService->process(
                user: $this->user,
                agent: $this->agent,
                conversation: $this->conversation,
                userMessage: $this->userMessage,
                brandId: $this->brandId,
                mode: $this->mode,
                imageUrl: $this->imageUrl
            );

            if (!$result->success) {
                Log::warning('AI Chat processing returned failure', [
                    'agent_id' => $this->agent->id,
                    'error' => $result->error,
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('AI Chat job exception', [
                'agent_id' => $this->agent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Ensure error message is saved to conversation
            $this->conversation->addMessage(
                'assistant', 
                'An error occurred while processing your request.'
            );
        }
    }

    /**
     * Set the tenant context for multi-tenancy support.
     */
    protected function setTenantContext(): void
    {
        if ($this->user->tenant) {
            app()->instance(\App\Models\Tenant::class, $this->user->tenant);
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AI Chat job failed permanently', [
            'agent_id' => $this->agent->id,
            'error' => $exception->getMessage(),
        ]);

        // Ensure user gets feedback even on job failure
        try {
            $this->conversation->addMessage(
                'assistant',
                'I apologize, but I was unable to process your request. Please try again.'
            );
        } catch (\Throwable $e) {
            // Silently fail - conversation may be in bad state
        }
    }
}
