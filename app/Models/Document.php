<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'type',
        'category',
        'status',
        'size',
        'path',
        'content',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signatureRequests(): HasMany
    {
        return $this->hasMany(SignatureRequest::class);
    }

    public function getSignatureStatusAttribute(): string
    {
        return $this->metadata['signature_status'] ?? 'unsigned';
    }

    public function isUnsigned(): bool
    {
        return $this->signatureStatus === 'unsigned';
    }

    public function isPendingSignature(): bool
    {
        return $this->signatureStatus === 'pending';
    }

    public function isSigned(): bool
    {
        return $this->signatureStatus === 'signed';
    }

    public function getLatestSignatureRequest(): ?SignatureRequest
    {
        return $this->signatureRequests()->latest()->first();
    }
}