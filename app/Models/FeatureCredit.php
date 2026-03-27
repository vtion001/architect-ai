<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FeatureType;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Feature Credit Model
 *
 * Tracks per-user, per-feature usage with monthly reset capability.
 *
 * CREDIT LIMITS:
 * - limit = -1: Unlimited usage (Pro/Agency plans)
 * - limit = 0: Feature disabled for this user
 * - limit > 0: Limited usage (Starter plan)
 *
 * TENANT ISOLATION:
 * - Uses BelongsToTenant trait for automatic global scoping
 * - Each credit record is tied to a specific tenant and user
 *
 * @property string $id
 * @property string $tenant_id
 * @property string $user_id
 * @property string $feature_type
 * @property int $limit
 * @property int $used
 * @property \Carbon\Carbon|null $reset_at
 */
class FeatureCredit extends Model
{
    use BelongsToTenant, HasUuids;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'feature_type',
        'limit',
        'used',
        'reset_at',
    ];

    protected $casts = [
        'limit' => 'integer',
        'used' => 'integer',
        'reset_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the feature type as an enum.
     */
    public function getFeatureTypeEnum(): ?FeatureType
    {
        return FeatureType::tryFrom($this->feature_type);
    }

    /**
     * Check if the user has credits remaining for this feature.
     */
    public function hasCreditsRemaining(): bool
    {
        // Unlimited
        if ($this->limit === -1) {
            return true;
        }

        // Feature disabled
        if ($this->limit === 0) {
            return false;
        }

        return $this->used < $this->limit;
    }

    /**
     * Get the number of credits remaining.
     */
    public function creditsRemaining(): int
    {
        if ($this->limit === -1) {
            return PHP_INT_MAX; // Effectively unlimited
        }

        return max(0, $this->limit - $this->used);
    }

    /**
     * Consume credits for this feature.
     *
     * @param  int  $amount  Number of credits to consume
     * @return bool True if credits were consumed, false if insufficient
     */
    public function consume(int $amount = 1): bool
    {
        // Check if we have enough credits
        if ($this->limit !== -1 && ($this->used + $amount) > $this->limit) {
            return false;
        }

        // For unlimited, don't increment used (keep at 0)
        if ($this->limit === -1) {
            return true;
        }

        $this->increment('used', $amount);

        return true;
    }

    /**
     * Reset the usage counter (for monthly resets).
     */
    public function resetUsage(): void
    {
        $this->update([
            'used' => 0,
            'reset_at' => now()->addMonth()->startOfMonth(),
        ]);
    }

    /**
     * Check if credits should be reset based on reset_at timestamp.
     */
    public function shouldReset(): bool
    {
        if (! $this->reset_at) {
            return false;
        }

        return now()->gte($this->reset_at);
    }
}
