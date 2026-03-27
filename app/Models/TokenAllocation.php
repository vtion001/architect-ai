<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TokenAllocation extends Model
{
    use BelongsToTenant, HasUuids;

    protected $fillable = [
        'tenant_id',
        'balance',
        'allocated_at',
        'expires_at',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
