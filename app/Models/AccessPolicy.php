<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AccessPolicy extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'effect',
        'conditions',
        'priority',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];
}