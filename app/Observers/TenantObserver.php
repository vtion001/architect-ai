<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Tenant;
use App\Services\FeatureCreditService;
use App\Services\TokenService;
use Illuminate\Support\Facades\Log;

/**
 * Observer for Tenant model.
 * 
 * Handles automated setup for new tenants:
 * - Granting initial tokens
 * - Provisioning feature credits for users
 */
class TenantObserver
{
    public function __construct(
        protected TokenService $tokenService,
        protected FeatureCreditService $featureCreditService
    ) {}

    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        $plan = $tenant->plan ?? 'starter';
        $amount = config("features.plans.{$plan}.monthly_tokens", config('tokens.initial_grant', 5000));
        
        Log::info('TenantObserver: Granting initial tokens', [
            'tenant_id' => $tenant->id,
            'plan' => $plan,
            'amount' => $amount,
        ]);

        try {
            $this->tokenService->grant($tenant, $amount, 'Welcome Bonus (Signup)');
        } catch (\Exception $e) {
            Log::error('TenantObserver: Failed to grant initial tokens', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Tenant "updated" event.
     * 
     * When a tenant's plan changes, update feature credits for all users.
     */
    public function updated(Tenant $tenant): void
    {
        // Check if the plan was changed
        if ($tenant->isDirty('plan')) {
            $oldPlan = $tenant->getOriginal('plan');
            $newPlan = $tenant->plan;

            Log::info('TenantObserver: Plan changed, upgrading credits', [
                'tenant_id' => $tenant->id,
                'old_plan' => $oldPlan,
                'new_plan' => $newPlan,
            ]);

            try {
                $this->featureCreditService->upgradeCreditsForTenant($tenant);
            } catch (\Exception $e) {
                Log::error('TenantObserver: Failed to upgrade credits', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

