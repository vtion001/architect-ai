<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Tenant;
use App\Services\TokenService;
use Illuminate\Support\Facades\Log;

/**
 * Observer for Tenant model.
 * 
 * Handles automated setup for new tenants, such as granting initial tokens.
 */
class TenantObserver
{
    public function __construct(protected TokenService $tokenService)
    {
    }

    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        $amount = config('tokens.initial_grant', 5000);
        
        Log::info('TenantObserver: Granting initial tokens', [
            'tenant_id' => $tenant->id,
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
}
