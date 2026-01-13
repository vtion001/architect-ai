<?php

namespace App\Providers;

use App\Models\AiAgent;
use App\Models\Brand;
use App\Models\Content;
use App\Observers\AiAgentObserver;
use App\Observers\ContentObserver;
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
     * The observer mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $observers = [
        Content::class => ContentObserver::class,
        AiAgent::class => AiAgentObserver::class,
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
        // Runtime DB Fix for Digital Ocean / Misconfigured Envs
        // If config is cached with 'db' (Docker default) but we are in an env where 'db' doesn't resolve
        if (config('database.connections.mysql.host') === 'db' && app()->environment('production')) {
            // Attempt to fallback to localhost or respect DATABASE_URL if processed late
            config(['database.connections.mysql.host' => '127.0.0.1']);
        }

        // Register policies manually for explicit binding
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Register model observers (Observer Pattern)
        foreach ($this->observers as $model => $observer) {
            $model::observe($observer);
        }

        Gate::define('is-developer', function ($user) {
            // Check by explicit attribute OR by Role
            return $user->is_developer || $user->roles()->where('name', 'Developer')->exists();
        });
    }
}