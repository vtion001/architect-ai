<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenLimit extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'type',
        'amount',
        'used',
        'reset_at',
    ];

    protected $casts = [
        'reset_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the user has reached their limit.
     */
    public function hasReachedLimit(int $additionalAmount = 0): bool
    {
        if ($this->type === 'unlimited') {
            return false;
        }

        return ($this->used + $additionalAmount) > $this->amount;
    }
}
