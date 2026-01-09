<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentBuilderController;
use App\Http\Controllers\ResearchEngineController;
use App\Http\Controllers\ContentCreatorController;
use App\Http\Controllers\SocialPlannerController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\AnalyticsController;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DeveloperController;

use App\Http\Controllers\LandingPageController;

use App\Http\Controllers\Auth\InvitationController;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/waitlist', [LandingPageController::class, 'waitlist'])->name('waitlist');
Route::post('/waitlist', [LandingPageController::class, 'joinWaitlist'])->name('waitlist.join');

// Invitation Protocol
Route::get('/auth/join/{token}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/auth/join/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');

// Redirect from old root if needed, but the user wants the landing page at /
// Route::get('/', function () {
//     return redirect('/dashboard');
// });

// Auth Routes
Route::prefix('auth')->group(function () {
    Route::get('register', function () { return view('auth.register'); })->name('register');
    Route::get('login/{tenant_slug?}', function ($slug = null) { return view('auth.login', ['slug' => $slug]); })->name('login');
    
    Route::get('join/{token}', [\App\Http\Controllers\Auth\InvitationController::class, 'show'])->name('invitation.join');
    Route::post('join/{token}', [\App\Http\Controllers\Auth\InvitationController::class, 'accept']);
    
    Route::post('register-agency', [AuthController::class, 'registerAgency'])->middleware('throttle:3,60'); // 3 agencies per hour
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,15'); // 5 attempts per 15 mins
    Route::post('logout', function () { auth()->logout(); return redirect('/auth/login'); })->name('logout');

    // MFA Challenge/Setup (Auth only, no mfa middleware)
    Route::middleware('auth')->group(function () {
        Route::get('mfa/challenge', [\App\Http\Controllers\Auth\MfaController::class, 'challenge'])->name('mfa.challenge');
        Route::post('mfa/verify', [\App\Http\Controllers\Auth\MfaController::class, 'verify'])->middleware('throttle:5,15')->name('mfa.verify');
        Route::get('mfa/setup', [\App\Http\Controllers\Auth\MfaController::class, 'setup'])->name('mfa.setup');
        Route::post('mfa/enable', [\App\Http\Controllers\Auth\MfaController::class, 'enable'])->name('mfa.enable');
    });
});

// Protected Workspace Routes
Route::middleware(['auth', 'tenant', 'mfa', 'session_security'])->group(function () {
    
    // Content & Intelligence Registry
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read-all');
    
    Route::get('/tenant/switch/{tenant}', [\App\Http\Controllers\Auth\TenantController::class, 'switch'])->name('tenant.switch');
    Route::post('/agency/impersonate', [\App\Http\Controllers\Auth\AgencyImpersonationController::class, 'impersonate'])->name('agency.impersonate');
    Route::get('/agency/impersonate/stop', [\App\Http\Controllers\Auth\AgencyImpersonationController::class, 'stop'])->name('agency.impersonate.stop');

    // Tenant/Agency Management
    Route::prefix('settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/branding', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateBranding'])->name('settings.branding');
        Route::post('/profile', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/mfa/disable', [\App\Http\Controllers\Tenant\SettingsController::class, 'disableMfa'])->name('settings.mfa.disable');

        Route::get('/sub-accounts', [\App\Http\Controllers\Tenant\SubAccountController::class, 'index'])->name('sub-accounts.index');
        Route::post('/sub-accounts', [\App\Http\Controllers\Tenant\SubAccountController::class, 'store'])->name('sub-accounts.store');
        
        Route::get('/users', [\App\Http\Controllers\Tenant\UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\Tenant\UserManagementController::class, 'store'])->name('users.store');
        Route::post('/users/invite', [\App\Http\Controllers\Tenant\UserManagementController::class, 'invite'])->name('users.invite');

        Route::post('/api/generate', [\App\Http\Controllers\Tenant\SettingsController::class, 'generateToken'])->name('settings.api.generate');
        Route::delete('/api/{token}', [\App\Http\Controllers\Tenant\SettingsController::class, 'revokeToken'])->name('settings.api.revoke');

        Route::get('/policies', [\App\Http\Controllers\Tenant\PolicyController::class, 'index'])->name('policies.index');
        Route::get('/policies/create', [\App\Http\Controllers\Tenant\PolicyController::class, 'create'])->name('policies.create');
        Route::post('/policies', [\App\Http\Controllers\Tenant\PolicyController::class, 'store'])->name('policies.store');
        Route::delete('/policies/{policy}', [\App\Http\Controllers\Tenant\PolicyController::class, 'destroy'])->name('policies.destroy');
    });

    Route::get('/document-builder', [DocumentBuilderController::class, 'index'])->name('document-builder.index');
    Route::post('/document-builder/generate', [DocumentBuilderController::class, 'generate'])->name('document-builder.generate');
    Route::get('/document-builder/preview', [DocumentBuilderController::class, 'preview'])->name('document-builder.preview');

    Route::get('/content-creator', [ContentCreatorController::class, 'index'])->name('content-creator.index');
    Route::post('/content-creator/generate', [ContentCreatorController::class, 'store'])->middleware('throttle:10,1')->name('content-creator.generate');
    Route::post('/content-creator/suggestions', [ContentCreatorController::class, 'getSuggestions'])->name('content-creator.suggestions');
    Route::post('/content-creator/refine', [ContentCreatorController::class, 'refineContext'])->name('content-creator.refine');
    Route::post('/content-creator/upload-media', [ContentCreatorController::class, 'uploadMedia'])->name('content-creator.upload-media');
    Route::post('/content-creator/generate-media', [ContentCreatorController::class, 'generateMedia'])->middleware('throttle:5,1')->name('content-creator.generate-media');
    Route::post('/content-creator/regenerate', [ContentCreatorController::class, 'regenerate'])->middleware('throttle:10,1')->name('content-creator.regenerate');
    Route::post('/content-creator/publish', [ContentCreatorController::class, 'publish'])->name('content-creator.publish');
    Route::post('/content-creator/{content}/save-visual', [ContentCreatorController::class, 'saveVisual'])->name('content-creator.save-visual');
    Route::delete('/content-creator/{content}', [ContentCreatorController::class, 'destroy'])->name('content-creator.destroy');
    Route::get('/content-creator/{content}', [ContentCreatorController::class, 'show'])->name('content-creator.show');

    Route::get('/social-planner', [SocialPlannerController::class, 'index'])->name('social-planner.index');
    Route::post('/social-planner/store', [SocialPlannerController::class, 'store'])->name('social-planner.store');
    Route::put('/social-planner/{content}', [SocialPlannerController::class, 'update'])->name('social-planner.update');
    Route::delete('/social-planner/{content}', [SocialPlannerController::class, 'destroy'])->name('social-planner.destroy');
    Route::post('/social-planner/suggestions', [SocialPlannerController::class, 'getSuggestions'])->name('social-planner.suggestions');
    Route::get('/social-planner/facebook-pages', [SocialPlannerController::class, 'getFacebookPages'])->name('social-planner.facebook-pages');
    Route::get('/social/callback/{platform}', [SocialPlannerController::class, 'handleCallback'])->name('social.callback');

    Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
    Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store'])->name('knowledge-base.store');
    Route::delete('/knowledge-base/{asset}', [KnowledgeBaseController::class, 'destroy'])->name('knowledge-base.destroy');

    Route::get('/ai-agents', [\App\Http\Controllers\AiAgentController::class, 'index'])->name('ai-agents.index');
    Route::post('/ai-agents', [\App\Http\Controllers\AiAgentController::class, 'store'])->name('ai-agents.store');
    Route::post('/ai-agents/chat', [\App\Http\Controllers\AiAgentController::class, 'chat'])->name('ai-agents.chat');
    Route::put('/ai-agents/{agent}', [\App\Http\Controllers\AiAgentController::class, 'update'])->name('ai-agents.update');
    Route::delete('/ai-agents/{agent}', [\App\Http\Controllers\AiAgentController::class, 'destroy'])->name('ai-agents.destroy');

    Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [DocumentsController::class, 'show'])->name('documents.show');
    Route::put('/documents/{document}', [DocumentsController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentsController::class, 'destroy'])->name('documents.destroy');

    Route::get('/media-registry', [\App\Http\Controllers\MediaRegistryController::class, 'index'])->name('media-registry.index');
    Route::post('/media-registry', [\App\Http\Controllers\MediaRegistryController::class, 'store'])->name('media-registry.store');
    Route::delete('/media-registry/{asset}', [\App\Http\Controllers\MediaRegistryController::class, 'destroy'])->name('media-registry.destroy');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/research-engine', [ResearchEngineController::class, 'index'])->name('research-engine.index');
    Route::post('/research-engine/start', [ResearchEngineController::class, 'store'])->middleware('throttle:5,1')->name('research-engine.start');
    Route::get('/research-engine/{research}', [ResearchEngineController::class, 'show'])->name('research-engine.show');
    Route::delete('/research-engine/{research}', [ResearchEngineController::class, 'destroy'])->name('research-engine.destroy');

    // Developer Only Routes
    Route::middleware('can:is-developer')->prefix('developer')->group(function () {
        Route::post('impersonate', [DeveloperController::class, 'impersonate'])->name('developer.impersonate');
        Route::post('stop-impersonating', [DeveloperController::class, 'stopImpersonating'])->name('developer.stop-impersonating');
    });

    // Platform Operations Console (Admin/DevTool)
    Route::middleware('can:is-developer')->prefix('admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/toggle-observability', [\App\Http\Controllers\Admin\AdminController::class, 'toggleObservability'])->name('admin.toggle-observability');
        Route::post('/waitlist/{lead}/convert', [\App\Http\Controllers\Admin\AdminController::class, 'convertLead'])->name('admin.waitlist.convert');
        
        Route::get('/audit', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('admin.audit.index');
        
        Route::get('/tenants', [\App\Http\Controllers\Admin\TenantExplorerController::class, 'index'])->name('admin.tenants.index');
        Route::get('/tenants/{tenant}', [\App\Http\Controllers\Admin\TenantExplorerController::class, 'show'])->name('admin.tenants.show');
        Route::post('/tenants/{tenant}/grant', [\App\Http\Controllers\Admin\TenantExplorerController::class, 'grantTokens'])->name('admin.tenants.grant');
    });

    // God View - Waitlist Management (admin@archit-ai.io only)
    Route::prefix('god-view')->group(function () {
        Route::get('/', [\App\Http\Controllers\GodViewController::class, 'index'])->name('god-view.index');
        Route::post('/approve/{waitlist}', [\App\Http\Controllers\GodViewController::class, 'approve'])->name('god-view.approve');
        Route::post('/reject/{waitlist}', [\App\Http\Controllers\GodViewController::class, 'reject'])->name('god-view.reject');
        Route::delete('/{waitlist}', [\App\Http\Controllers\GodViewController::class, 'destroy'])->name('god-view.destroy');
        Route::post('/bulk-approve', [\App\Http\Controllers\GodViewController::class, 'bulkApprove'])->name('god-view.bulk-approve');
    });
});
