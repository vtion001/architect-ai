<?php

namespace App\Console\Commands;

use App\Models\TokenLimit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefillUserQuotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:reset-quotas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset monthly user token quotas and clear usage logs.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting token quota reset...');

        $affected = TokenLimit::where('type', 'monthly')
            ->where(function ($query) {
                $query->whereNull('reset_at')
                    ->orWhere('reset_at', '<=', now());
            })
            ->update([
                'used' => 0,
                'reset_at' => now()->addMonth()->startOfMonth(),
            ]);

        $this->info("Successfully reset quotas for {$affected} users.");

        Log::info('RefillUserQuotas: Monthly reset completed', [
            'users_affected' => $affected,
        ]);

        return Command::SUCCESS;
    }
}
