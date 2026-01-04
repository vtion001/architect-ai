<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasUuids;

    public $timestamps = false; // Uses custom timestamp field

    protected $fillable = [
        'timestamp',
        'actor_id',
        'actor_type',
        'tenant_id',
        'action',
        'resource_type',
        'resource_id',
        'result',
        'ip_address',
        'metadata',
        'justification',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'metadata' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
