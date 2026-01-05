<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DeveloperController;
use App\Http\Controllers\ContentCreatorController;
use App\Http\Controllers\ResearchEngineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register-agency', [AuthController::class, 'registerAgency'])->middleware('throttle:3,60');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,15');
});

/*
|--------------------------------------------------------------------------
| Tenant-Scoped Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'tenant', 'session_security'])->group(function () {
    
    // User Info
    Route::get('/me', function () {
        return auth()->user()->load('tenant', 'roles.permissions');
    });

    // Developer Only Routes
    Route::middleware('can:is-developer')->prefix('developer')->group(function () {
        Route::post('impersonate', [DeveloperController::class, 'impersonate']);
        Route::post('stop-impersonating', [DeveloperController::class, 'stopImpersonating']);
    });

    // Content & Intelligence
    Route::post('/content/generate', [ContentCreatorController::class, 'store']);
    Route::post('/research/start', [ResearchEngineController::class, 'store']);
    
    // Global Grid Search (for Command Palette)
    Route::get('/search', [\App\Http\Controllers\GlobalSearchController::class, 'index']);
});