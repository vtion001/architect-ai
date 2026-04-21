<?php

namespace App\Providers;

use App\Models\AiAgent;
use App\Models\Brand;
use App\Models\Content;
use App\Models\Tenant;
use App\Observers\AiAgentObserver;
use App\Observers\ContentObserver;
use App\Observers\TenantObserver;
use App\Policies\AiAgentPolicy;
use App\Policies\BrandPolicy;
use App\View\Composers\ContentViewerComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Tenant::class => TenantObserver::class,
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

        // Register model observers (Observer Pattern)
        foreach ($this->observers as $model => $observer) {
            $model::observe($observer);
        }

        Gate::define('is-developer', function ($user) {
            // Check by explicit attribute OR by Role
            return $user->is_developer || $user->roles()->where('name', 'Developer')->exists();
        });

        // Disable SSL verification for local dev (Windows lacks CA bundle)
        if (app()->environment('local')) {
            Http::globalOptions(['verify' => false]);
        }

        // Register View Composers
        View::composer('content-creator.content-viewer', ContentViewerComposer::class);
    }
}
