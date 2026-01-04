<?php

namespace App\Services;

use App\Models\TokenAllocation;
use App\Models\TokenTransaction;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TokenService
{
    /**
     * Get the current token balance for a tenant.
     */
    public function getBalance(Tenant $tenant): int
    {
        return TokenAllocation::where('tenant_id', $tenant->id)->sum('balance');
    }

    /**
     * Consume tokens for a specific action.
     */
    public function consume(User $user, int $amount, string $reason, array $metadata = []): bool
    {
        // 1. Developer bypass (free for observability, but audited)
        if ($user->is_developer) {
            return true;
        }

        return DB::transaction(function () use ($user, $amount, $reason, $metadata) {
            $allocation = TokenAllocation::where('tenant_id', $user->tenant_id)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->where('balance', '>=', $amount)
                ->first();

            if (!$allocation) {
                return false;
            }

            $allocation->decrement('balance', $amount);

            TokenTransaction::create([
                'tenant_id' => $user->tenant_id,
                'user_id' => $user->id,
                'amount' => -$amount,
                'balance_after' => $this->getBalance($user->tenant),
                'reason' => $reason,
                'metadata' => $metadata,
            ]);

            return true;
        });
    }

    /**
     * Grant tokens to a tenant.
     */
    public function grant(Tenant $tenant, int $amount, string $reason, ?string $expiresAt = null)
    {
        return DB::transaction(function () use ($tenant, $amount, $reason, $expiresAt) {
            $allocation = TokenAllocation::create([
                'tenant_id' => $tenant->id,
                'balance' => $amount,
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
}
