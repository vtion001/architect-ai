<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AiAgent;
use App\Models\AgentConversation;
use Illuminate\Support\Facades\Log;

/**
 * Observer for AiAgent model.
 * 
 * Handles side effects like cleanup when agents are deleted.
 */
class AiAgentObserver
{
    /**
     * Handle the AiAgent "created" event.
     */
    public function created(AiAgent $agent): void
    {
        Log::info('AiAgentObserver: New AI agent created', [
            'id' => $agent->id,
            'tenant_id' => $agent->tenant_id,
            'name' => $agent->name,
            'role' => $agent->role,
        ]);
    }

    /**
     * Handle the AiAgent "deleting" event.
     * 
     * Clean up conversations before agent is deleted.
     */
    public function deleting(AiAgent $agent): void
    {
        // Delete all conversations for this agent
        $deletedCount = AgentConversation::where('agent_id', $agent->id)->delete();
        
        Log::info('AiAgentObserver: Cleaning up agent conversations', [
            'agent_id' => $agent->id,
            'conversations_deleted' => $deletedCount,
        ]);
    }

    /**
     * Handle the AiAgent "deleted" event.
     */
    public function deleted(AiAgent $agent): void
    {
        Log::info('AiAgentObserver: AI agent deleted', [
            'id' => $agent->id,
            'name' => $agent->name,
        ]);
    }
}
