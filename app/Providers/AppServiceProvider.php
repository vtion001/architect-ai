<?php

namespace App\Providers;

use App\Models\AiAgent;
use App\Models\Brand;
use App\Policies\AiAgentPolicy;
use App\Policies\BrandPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Brand::class => BrandPolicy::class,
        AiAgent::class => AiAgentPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies manually for explicit binding
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        Gate::define('is-developer', function ($user) {
            // Check by explicit attribute OR by Role
            return $user->is_developer || $user->roles()->where('name', 'Developer')->exists();
        });
    }
}