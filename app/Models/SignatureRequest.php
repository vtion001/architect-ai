<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatureRequest extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'signer_name',
        'signer_email',
        'subject',
        'message',
        'hellosign_signature_request_id',
        'status',
        'signature_token',
        'sent_at',
        'viewed_at',
        'signed_at',
        'signature_data',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'signed_at' => 'datetime',
        'signature_data' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsViewed(): void
    {
        if (! $this->viewed_at) {
            $this->update([
                'status' => 'viewed',
                'viewed_at' => now(),
            ]);
        }
    }

    public function markAsSigned(array $signatureData = []): void
    {
        $this->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_data' => $signatureData,
        ]);

        // Update document metadata
        $metadata = $this->document->metadata ?? [];
        $metadata['signature_status'] = 'signed';
        $metadata['signed_at'] = now()->toIso8601String();
        $metadata['signed_by'] = $this->signer_email;

        $this->document->update(['metadata' => $metadata]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }
}
