<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('is-developer', function ($user) {
            // Check by explicit attribute OR by Role
            return $user->is_developer || $user->roles()->where('name', 'Developer')->exists();
        });
    }
}