<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FeatureType;
use App\Models\FeatureCredit;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Feature Credit Service
 *
 * Manages feature credit operations including:
 * - Provisioning initial credits for new users
 * - Checking feature access and credit availability
 * - Consuming credits for feature usage
 * - Monthly credit resets
 *
 * SECURITY:
 * - Developer accounts bypass all restrictions
 * - All operations are tenant-scoped
 * - Credit consumption is atomic (DB transaction)
 */
class FeatureCreditService
{
    /**
     * Check if a user is exempt from credit restrictions.
     * Developer accounts have unlimited access to all features.
     */
    public function isDeveloperBypass(User $user): bool
    {
        if (! config('features.developer_bypass', true)) {
            return false;
        }

        return $user->is_developer;
    }

    /**
     * Provision feature credits for a new user based on their tenant's plan.
     */
    public function provisionCreditsForUser(User $user): void
    {
        $tenant = $user->tenant;

        if (! $tenant) {
            Log::warning('FeatureCreditService: Cannot provision credits - no tenant', [
                'user_id' => $user->id,
            ]);

            return;
        }

        $plan = $tenant->plan ?? 'starter';
        $credits = config("features.plans.{$plan}.credits", []);

        Log::info('FeatureCreditService: Provisioning credits for user', [
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'plan' => $plan,
            'credits' => $credits,
        ]);

        DB::transaction(function () use ($user, $tenant, $credits) {
            foreach ($credits as $feature => $limit) {
                FeatureCredit::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'tenant_id' => $tenant->id,
                        'feature_type' => $feature,
                    ],
                    [
                        'limit' => $limit,
                        'used' => 0,
                        'reset_at' => now()->addMonth()->startOfMonth(),
                    ]
                );
            }
        });
    }

    /**
     * Check if a user can use a specific feature.
     *
     * For credit-based features: checks remaining credits
     * For access-gated features: checks plan access
     */
    public function canUseFeature(User $user, FeatureType $feature): bool
    {
        // Developer bypass
        if ($this->isDeveloperBypass($user)) {
            return true;
        }

        $tenant = $user->tenant;

        if (! $tenant) {
            return false;
        }

        // For access-gated features, check plan access
        if ($feature->isAccessGated()) {
            return $tenant->canAccessFeature($feature);
        }

        // For credit-based features, check credits
        $credit = $this->getUserCredit($user, $feature);

        if (! $credit) {
            // No credit record - provision and check again
            $this->provisionCreditsForUser($user);
            $credit = $this->getUserCredit($user, $feature);
        }

        // Auto-reset if needed
        if ($credit && $credit->shouldReset()) {
            $credit->resetUsage();
        }

        return $credit?->hasCreditsRemaining() ?? false;
    }

    /**
     * Consume a credit for a feature usage.
     *
     * @return bool True if credit was consumed, false if insufficient credits
     */
    public function consumeCredit(User $user, FeatureType $feature, int $amount = 1): bool
    {
        // Developer bypass - don't consume credits
        if ($this->isDeveloperBypass($user)) {
            Log::info('FeatureCreditService: Developer bypass - credit not consumed', [
                'user_id' => $user->id,
                'feature' => $feature->value,
            ]);

            return true;
        }

        $credit = $this->getUserCredit($user, $feature);

        if (! $credit) {
            Log::warning('FeatureCreditService: No credit record found', [
                'user_id' => $user->id,
                'feature' => $feature->value,
            ]);

            return false;
        }

        // Auto-reset if needed
        if ($credit->shouldReset()) {
            $credit->resetUsage();
        }

        $consumed = $credit->consume($amount);

        if ($consumed) {
            Log::info('FeatureCreditService: Credit consumed', [
                'user_id' => $user->id,
                'feature' => $feature->value,
                'amount' => $amount,
                'remaining' => $credit->creditsRemaining(),
            ]);
        } else {
            Log::info('FeatureCreditService: Credit limit reached', [
                'user_id' => $user->id,
                'feature' => $feature->value,
                'limit' => $credit->limit,
                'used' => $credit->used,
            ]);
        }

        return $consumed;
    }

    /**
     * Get the credit record for a user and feature.
     */
    public function getUserCredit(User $user, FeatureType $feature): ?FeatureCredit
    {
        return FeatureCredit::withoutGlobalScope('tenant')
            ->where('user_id', $user->id)
            ->where('feature_type', $feature->value)
            ->first();
    }

    /**
     * Get all credits for a user.
     */
    public function getUserCredits(User $user): array
    {
        $credits = FeatureCredit::withoutGlobalScope('tenant')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('feature_type');

        $result = [];

        foreach (FeatureType::cases() as $feature) {
            if ($feature->isCreditBased()) {
                $credit = $credits->get($feature->value);
                $result[$feature->value] = [
                    'label' => $feature->label(),
                    'limit' => $credit?->limit ?? 0,
                    'used' => $credit?->used ?? 0,
                    'remaining' => $credit?->creditsRemaining() ?? 0,
                    'unlimited' => ($credit?->limit ?? 0) === -1,
                    'reset_at' => $credit?->reset_at?->toIso8601String(),
                ];
            }
        }

        return $result;
    }

    /**
     * Reset credits for all users of a tenant (e.g., after plan upgrade).
     */
    public function resetCreditsForTenant(Tenant $tenant): void
    {
        $plan = $tenant->plan ?? 'starter';
        $credits = config("features.plans.{$plan}.credits", []);

        $tenant->loadMissing('users');

        DB::transaction(function () use ($tenant, $credits) {
            foreach ($tenant->users as $user) {
                foreach ($credits as $feature => $limit) {
                    FeatureCredit::withoutGlobalScope('tenant')
                        ->where('user_id', $user->id)
                        ->where('feature_type', $feature)
                        ->update([
                            'limit' => $limit,
                            'used' => 0,
                            'reset_at' => now()->addMonth()->startOfMonth(),
                        ]);
                }
            }
        });

        Log::info('FeatureCreditService: Reset credits for tenant', [
            'tenant_id' => $tenant->id,
            'plan' => $plan,
        ]);
    }

    /**
     * Upgrade credits for a tenant when their plan changes.
     */
    public function upgradeCreditsForTenant(Tenant $tenant): void
    {
        $plan = $tenant->plan ?? 'starter';
        $credits = config("features.plans.{$plan}.credits", []);

        $tenant->loadMissing('users');

        DB::transaction(function () use ($tenant, $credits) {
            foreach ($tenant->users as $user) {
                foreach ($credits as $feature => $limit) {
                    FeatureCredit::withoutGlobalScope('tenant')
                        ->updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'tenant_id' => $tenant->id,
                                'feature_type' => $feature,
                            ],
                            [
                                'limit' => $limit,
                                // Don't reset 'used' on upgrade - only update limit
                                'reset_at' => now()->addMonth()->startOfMonth(),
                            ]
                        );
                }
            }
        });

        Log::info('FeatureCreditService: Upgraded credits for tenant', [
            'tenant_id' => $tenant->id,
            'plan' => $plan,
        ]);
    }
}
