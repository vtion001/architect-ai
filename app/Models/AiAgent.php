<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AI Agent Model
 * 
 * Represents a customizable AI assistant for a tenant.
 * 
 * TENANT ISOLATION:
 * - Uses BelongsToTenant trait for automatic global scoping
 * - getKnowledgeContext() explicitly validates tenant_id for RAG data
 * - Conversations are scoped via AgentConversation model
 * 
 * TOKEN CONSUMPTION:
 * - AI chat messages consume tokens via ProcessAiChatMessage job
 * - Cost: TokenService::COSTS['ai_chat_message'] = 5 tokens per message
 * 
 * @property string $id
 * @property string $tenant_id
 * @property string $user_id
 * @property string $name
 * @property string $role
 * @property string|null $goal
 * @property string|null $backstory
 * @property array|null $knowledge_sources
 * @property bool $is_active
 */
class AiAgent extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'role',
        'goal',
        'backstory',
        'knowledge_sources',
        'is_active',
        // Appearance customization
        'avatar_url',
        'primary_color',
        'welcome_message',
        // Behavior settings
        'model',
        'temperature',
        'max_tokens',
        'system_prompt',
        // Widget settings
        'widget_position',
        'widget_enabled',
        'allowed_domains',
    ];

    protected $casts = [
        'knowledge_sources' => 'array',
        'allowed_domains' => 'array',
        'is_active' => 'boolean',
        'widget_enabled' => 'boolean',
        'temperature' => 'float',
        'max_tokens' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'widget_enabled' => true,
        'primary_color' => '#00F2FF',
        'temperature' => 0.7,
        'max_tokens' => 2000,
        'widget_position' => 'bottom-right',
        'welcome_message' => 'Hello! How can I assist you today?',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AgentConversation::class, 'agent_id');
    }

    /**
     * Get the full system prompt including backstory and knowledge context.
     */
    public function getFullSystemPrompt(): string
    {
        $prompt = $this->system_prompt ?: "You are {$this->name}, a {$this->role}.";
        
        if ($this->goal) {
            $prompt .= "\n\nYour primary goal: {$this->goal}";
        }
        
        if ($this->backstory) {
            $prompt .= "\n\nContext: {$this->backstory}";
        }

        return $prompt;
    }

    /**
     * Get knowledge base content for RAG.
     * 
     * SECURITY: Only retrieves assets that belong to the same tenant
     * as this agent, preventing cross-tenant data leakage.
     */
    public function getKnowledgeContext(): ?string
    {
        if (empty($this->knowledge_sources)) {
            return null;
        }

        // SECURITY: Explicitly scope by tenant_id to prevent data leakage
        // even if knowledge_sources array is manipulated
        $assets = KnowledgeBaseAsset::query()
            ->where('tenant_id', $this->tenant_id)
            ->whereIn('id', $this->knowledge_sources)
            ->get();
        
        if ($assets->isEmpty()) {
            return null;
        }

        return $assets->map(fn($a) => "--- SOURCE: {$a->title} ---\n{$a->content}")
            ->implode("\n\n");
    }
}

