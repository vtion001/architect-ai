<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TokenAllocation;
use App\Models\TokenTransaction;
use App\Models\TokenLimit;
use App\Models\Tenant;
use App\Models\User;
use App\Exceptions\UserTokenLimitExceededException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Token Service
 * 
 * Manages token consumption, allocation, and balance tracking.
 * 
 * SECURITY: All operations are tenant-scoped to prevent cross-tenant access.
 * Token transactions are audited for compliance.
 * 
 * @see config/tokens.php for cost configuration
 */
class TokenService
{
    /**
     * Get the current token balance for a tenant.
     * 
     * SECURITY: Explicitly bypasses global scope and filters by tenant_id
     * to prevent any potential scope manipulation.
     */
    public function getBalance(Tenant $tenant): int
    {
        return (int) TokenAllocation::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->sum('balance');
    }

    /**
     * Check if a tenant has sufficient tokens.
     */
    public function hasBalance(Tenant $tenant, int $required): bool
    {
        return $this->getBalance($tenant) >= $required;
    }

    /**
     * Get or create the token limit for a user.
     */
    public function getUserLimit(User $user): TokenLimit
    {
        return TokenLimit::withoutGlobalScope('tenant')
            ->firstOrCreate(
                ['user_id' => $user->id, 'tenant_id' => $user->tenant_id],
                [
                    'type' => 'monthly',
                    'amount' => config('tokens.defaults.user_monthly_limit', 10000),
                    'used' => 0,
                    'reset_at' => now()->addMonth()->startOfMonth(),
                ]
            );
    }

    /**
     * Consume tokens for a specific action.
     * 
     * SECURITY: 
     * - Uses explicit tenant_id filter (not global scope)
     * - Validates user belongs to the tenant
     * - Logs all consumption for audit
     * 
     * @param User $user The user consuming tokens
     * @param int $amount Number of tokens to consume
     * @param string $reason Reason for consumption (audit trail)
     * @param array $metadata Additional metadata for the transaction
     * @return bool True if tokens were successfully consumed
     * @throws UserTokenLimitExceededException
     */
    public function consume(User $user, int $amount, string $reason, array $metadata = []): bool
    {
        // Developer bypass (for observability, but still logged)
        if ($user->is_developer) {
            Log::info('Token bypass (developer)', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
            ]);
            return true;
        }

        $tenant = $user->tenant;
        if (!$tenant) {
            Log::warning('Token consumption failed: No tenant', ['user_id' => $user->id]);
            return false;
        }

        return DB::transaction(function () use ($user, $tenant, $amount, $reason, $metadata) {
            // 1. Check Individual User Limit
            $limit = $this->getUserLimit($user);
            
            if ($limit->hasReachedLimit($amount)) {
                throw new UserTokenLimitExceededException($limit->amount, $limit->used);
            }

            // 2. Check Tenant Balance
            $allocation = TokenAllocation::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->where('balance', '>=', $amount)
                ->lockForUpdate()
                ->first();

            if (!$allocation) {
                Log::info('Insufficient tokens', [
                    'tenant_id' => $tenant->id,
                    'required' => $amount,
                ]);
                return false;
            }

            // 3. Execute Consumption
            $allocation->decrement('balance', $amount);
            $limit->increment('used', $amount);

            TokenTransaction::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'amount' => -$amount,
                'balance_after' => $this->getBalance($tenant),
                'reason' => $reason,
                'metadata' => array_merge($metadata, [
                    'ip' => request()->ip(),
                ]),
            ]);

            return true;
        });
    }

    /**
     * Grant tokens to a tenant.
     */
    public function grant(Tenant $tenant, int $amount, string $reason, ?string $expiresAt = null): TokenAllocation
    {
        return DB::transaction(function () use ($tenant, $amount, $reason, $expiresAt) {
            $allocation = TokenAllocation::create([
                'tenant_id' => $tenant->id,
                'balance' => $amount,
                'allocated_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            TokenTransaction::create([
                'tenant_id' => $tenant->id,
                'amount' => $amount,
                'balance_after' => $this->getBalance($tenant),
                'reason' => $reason,
            ]);

            return $allocation;
        });
    }

    /**
     * Refund tokens to a tenant.
     */
    public function refund(Tenant $tenant, int $amount, string $reason): void
    {
        $this->grant($tenant, $amount, "refund: {$reason}");
    }

    /**
     * Get the cost for a specific operation type from config.
     */
    public function getCost(string $operation): int
    {
        return (int) config("tokens.costs.{$operation}", 10);
    }

    /**
     * Get transaction history for a tenant.
     */
    public function getHistory(Tenant $tenant, int $limit = 50): Collection
    {
        return TokenTransaction::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
