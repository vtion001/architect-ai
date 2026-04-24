<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// ── Permanent SQLite auto-creation ──────────────────────────────────────────────
// Prevents "database file does not exist" errors after git clean / fresh clone.
// SQLite creates the file automatically on first connection, but only for existing
// parent directories. This ensures the file always exists before any DB query.
if (env('DB_CONNECTION') === 'sqlite') {
    // Use __DIR__ (bootstrap dir) and relative path — base_path() is not available yet
    $dbFile = env('DB_DATABASE', 'database/database.sqlite');
    // Skip file creation for in-memory SQLite (used in tests)
    if ($dbFile !== ':memory:') {
        $dbPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dbFile);
        if (!file_exists($dbPath)) {
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            touch($dbPath); // zero-byte file; SQLite auto-initialises on connect
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // Performance middleware (order matters: compress last)
        $middleware->append(\App\Http\Middleware\CacheHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\CompressionMiddleware::class);

        $middleware->alias([
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'mfa' => \App\Http\Middleware\MfaMiddleware::class,
            'session_security' => \App\Http\Middleware\SessionSecurityMiddleware::class,
            'feature' => \App\Http\Middleware\CheckFeatureAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
