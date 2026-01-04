<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasUuids;

    protected $fillable = [
        'type',
        'parent_id',
        'name',
        'slug',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subAccounts(): HasMany
    {
        return $this->hasMany(Tenant::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'parent_id');
    }
}
