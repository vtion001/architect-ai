<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\AuthorizationService;
use App\Services\TokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefillGridTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grid:refill-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch monthly token allocations based on grid tiers.';

    /**
     * Execute the console command.
     */
    public function handle(TokenService $tokenService, AuthorizationService $authService)
    {
        $this->info('INITIATING RESOURCE ALLOCATION PROTOCOL...');

        $tenants = Tenant::where('status', 'active')->get();
        $bar = $this->output->createProgressBar($tenants->count());

        $bar->start();

        foreach ($tenants as $tenant) {
            $plan = $tenant->plan ?? 'standard';
            $allocation = config("grid.tiers.{$plan}.monthly_tokens", 5000);

            // Provision the resources
            $tokenService->grant($tenant, $allocation, "Automated monthly refill: {$plan} node");

            // Log to system audit
            Log::info("GRID_RESOURCE: Provisioned {$allocation} tokens to tenant {$tenant->slug} (Tier: {$plan})");

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('RESOURCE PROTOCOL FINALIZED.');
    }
}
