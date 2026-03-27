<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AI\MiniMaxClient;
use App\Services\AI\OpenAIClient;
use App\Services\AI\PromptBuilder;
use Illuminate\Support\ServiceProvider;

/**
 * AI Service Provider
 *
 * Registers AI-related services for dependency injection.
 */
class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register as singletons for performance
        $this->app->singleton(MiniMaxClient::class, fn() => new MiniMaxClient());
        $this->app->singleton(OpenAIClient::class, fn() => new OpenAIClient());
        $this->app->singleton(PromptBuilder::class, fn() => new PromptBuilder());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
