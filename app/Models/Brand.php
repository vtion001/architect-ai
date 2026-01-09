<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brand extends Model
{
    use HasUuids;

    protected $fillable = [
        'tenant_id',
        'name',
        'logo_url',
        'colors',
        'typography',
        'voice_profile',
        'contact_info',
        'is_default',
    ];

    protected $casts = [
        'colors' => 'array',
        'typography' => 'array',
        'voice_profile' => 'array',
        'contact_info' => 'array',
        'is_default' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
