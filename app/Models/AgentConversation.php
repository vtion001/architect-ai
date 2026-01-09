<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentConversation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'agent_id',
        'session_id',
        'user_id',
        'messages',
        'metadata',
    ];

    protected $casts = [
        'messages' => 'array',
        'metadata' => 'array',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class, 'agent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add a message to the conversation.
     */
    public function addMessage(string $role, string $content): self
    {
        $messages = $this->messages ?? [];
        $messages[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toIso8601String(),
        ];
        
        $this->messages = $messages;
        $this->save();
        
        return $this;
    }

    /**
     * Get messages formatted for OpenAI API.
     */
    public function getMessagesForApi(): array
    {
        return collect($this->messages ?? [])->map(fn($msg) => [
            'role' => $msg['role'],
            'content' => $msg['content'],
        ])->toArray();
    }
}
