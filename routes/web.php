<?php

require __DIR__.'/api-docs.php';

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\TenantExplorerController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\AgencyImpersonationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DeveloperController;
use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\Auth\MfaController;
use App\Http\Controllers\Auth\TenantController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ContentCreatorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentBuilderController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\GodViewController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\MediaRegistryController;
use App\Http\Controllers\ResearchEngineController;
use App\Http\Controllers\SignatureRequestController;
use App\Http\Controllers\SocialPlannerController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Tenant\PolicyController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\SubAccountController;
use App\Http\Controllers\Tenant\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/waitlist', [LandingPageController::class, 'waitlist'])->name('waitlist');
Route::post('/waitlist', [LandingPageController::class, 'joinWaitlist'])->name('waitlist.join');

// Public Signature Routes (no auth required)
Route::get('/signatures/{token}/sign', [SignatureRequestController::class, 'show'])->name('signatures.sign');
Route::post('/signatures/{token}/submit', [SignatureRequestController::class, 'submit'])->name('signatures.submit');

// Invitation Protocol
Route::get('/auth/join/{token}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/auth/join/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');

// Redirect from old root if needed, but the user wants the landing page at /
// Route::get('/', function () {
//     return redirect('/dashboard');
// });

// Auth Routes
Route::prefix('auth')->group(function () {
    Route::get('register', function () {
        return view('auth.register');
    })->name('register');
    Route::get('login/{tenant_slug?}', function ($slug = null) {
        return view('auth.login', ['slug' => $slug]);
    })->name('login');

    Route::get('join/{token}', [InvitationController::class, 'show'])->name('invitation.join');
    Route::post('join/{token}', [InvitationController::class, 'accept']);

    Route::post('register-agency', [AuthController::class, 'registerAgency'])->middleware('throttle:3,60'); // 3 agencies per hour
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:60,1'); // 60 attempts per 1 min
    Route::post('logout', function () {
        auth()->logout();

        return redirect('/auth/login');
    })->name('logout');

    // MFA Challenge/Setup (Auth only, no mfa middleware)
    Route::middleware('auth')->group(function () {
        Route::get('mfa/challenge', [MfaController::class, 'challenge'])->name('mfa.challenge');
        Route::post('mfa/verify', [MfaController::class, 'verify'])->middleware('throttle:60,1')->name('mfa.verify');
        Route::get('mfa/setup', [MfaController::class, 'setup'])->name('mfa.setup');
        Route::post('mfa/enable', [MfaController::class, 'enable'])->name('mfa.enable');
    });
});

// Public Routes (No Auth Required)
Route::get('/api-docs', function () {
    return response()->file(public_path('api-docs.html'));
})->name('api-docs');

// Protected Workspace Routes
Route::middleware(['auth', 'tenant', 'mfa', 'session_security'])->group(function () {

    // Task & Note Management
    Route::resource('tasks', TaskController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('tasks/{task}/force', [TaskController::class, 'forceDelete'])->name('tasks.force-delete');
    Route::post('tasks/breakdown', [TaskController::class, 'breakdown'])->name('tasks.breakdown');
    Route::post('tasks/voice-to-intelligence', [TaskController::class, 'voiceToIntelligence'])->name('tasks.voice');
    Route::post('tasks/voice-save', [TaskController::class, 'saveAudioOnly'])->name('tasks.voice-save');

    // Ghost Demo Routes
    Route::post('tasks/ghost-demo', [TaskController::class, 'storeGhostDemo'])->name('tasks.ghost-demo.store');
    Route::get('tasks/ghost-demo/{document}', [TaskController::class, 'showGhostDemo'])->name('tasks.ghost-demo.show');
    Route::post('task-categories', [TaskController::class, 'storeCategory'])->name('task-categories.store');
    Route::delete('task-categories/{category}', [TaskController::class, 'destroyCategory'])->name('task-categories.destroy');

    // Content & Intelligence Registry
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API Documentation - moved to public routes
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    })->name('notifications.read-all');

    Route::get('/tenant/switch/{tenant}', [TenantController::class, 'switch'])->name('tenant.switch');
    Route::post('/agency/impersonate', [AgencyImpersonationController::class, 'impersonate'])->name('agency.impersonate');
    Route::get('/agency/impersonate/stop', [AgencyImpersonationController::class, 'stop'])->name('agency.impersonate.stop');

    // Billing & Subscription Management
    Route::prefix('billing')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/upgrade', [BillingController::class, 'upgrade'])->name('billing.upgrade');
        Route::get('/credits', [BillingController::class, 'credits'])->name('billing.credits');
        Route::get('/check/{feature}', [BillingController::class, 'checkFeature'])->name('billing.check-feature');
    });

    // Tenant/Agency Management
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/branding', [SettingsController::class, 'updateBranding'])->name('settings.branding');
        Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/mfa/disable', [SettingsController::class, 'disableMfa'])->name('settings.mfa.disable');

        // Brand Kits (Pro+ Feature)
        Route::middleware('feature:brand_kits')->group(function () {
            Route::post('/brands/scrape', [BrandController::class, 'scrape'])->name('brands.scrape');
            Route::post('/brands/analyze-blueprint', [BrandController::class, 'analyzeBlueprint'])->name('brands.analyze-blueprint');
            Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
            Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
            Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
            Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
            Route::post('/brands/{brand}/default', [BrandController::class, 'setDefault'])->name('brands.set-default');
        });

        // Sub-Accounts (Agency Only Feature)
        Route::middleware('feature:sub_accounts')->group(function () {
            Route::get('/sub-accounts', [SubAccountController::class, 'index'])->name('sub-accounts.index');
            Route::post('/sub-accounts', [SubAccountController::class, 'store'])->name('sub-accounts.store');
        });

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::post('/users/invite', [UserManagementController::class, 'invite'])->name('users.invite');

        Route::post('/api/generate', [SettingsController::class, 'generateToken'])->name('settings.api.generate');
        Route::delete('/api/{token}', [SettingsController::class, 'revokeToken'])->name('settings.api.revoke');

        Route::get('/policies', [PolicyController::class, 'index'])->name('policies.index');
        Route::get('/policies/create', [PolicyController::class, 'create'])->name('policies.create');
        Route::post('/policies', [PolicyController::class, 'store'])->name('policies.store');
        Route::delete('/policies/{policy}', [PolicyController::class, 'destroy'])->name('policies.destroy');
    });

    Route::get('/document-builder', [DocumentBuilderController::class, 'index'])->name('document-builder.index');
    Route::post('/document-builder/generate', [DocumentBuilderController::class, 'generate'])->name('document-builder.generate');
    Route::post('/document-builder/draft-cover-letter', [DocumentBuilderController::class, 'draftCoverLetter'])->name('document-builder.draft-cover-letter');
    Route::post('/document-builder/parse-resume', [DocumentBuilderController::class, 'parseResume'])->name('document-builder.parse-resume');
    Route::post('/document-builder/upload-photo', [DocumentBuilderController::class, 'uploadPhoto'])->name('document-builder.upload-photo');
    Route::post('/document-builder/preview', [DocumentBuilderController::class, 'preview'])->name('document-builder.preview');

    Route::get('/content-creator', [ContentCreatorController::class, 'index'])->name('content-creator.index');
    Route::post('/content-creator/generate', [ContentCreatorController::class, 'store'])->middleware('throttle:10,1')->name('content-creator.generate');
    Route::post('/content-creator/blog/batch', [ContentCreatorController::class, 'batchStore'])->middleware('throttle:10,1')->name('content-creator.blog.batch');
    // Bulk Operations (Moved from API for session auth)
    Route::post('/content-creator/generate-bulk-images', [ContentCreatorController::class, 'generateBulkImages'])->name('content-creator.generate-bulk-images');
    Route::post('/content-creator/bulk-schedule', [ContentCreatorController::class, 'bulkSchedule'])->name('content-creator.bulk-schedule');

    Route::post('/content-creator/suggestions', [ContentCreatorController::class, 'getSuggestions'])->name('content-creator.suggestions');
    Route::post('/content-creator/generate-blog-body', [ContentCreatorController::class, 'generateBlogBody'])->name('content-creator.generate-blog-body');
    Route::post('/content-creator/generate-image-prompt', [ContentCreatorController::class, 'generateImagePrompt'])->middleware('throttle:10,1')->name('content-creator.generate-image-prompt');
    Route::post('/content-creator/refine', [ContentCreatorController::class, 'refineContext'])->name('content-creator.refine');
    Route::post('/content-creator/upload-media', [ContentCreatorController::class, 'uploadMedia'])->name('content-creator.upload-media');
    Route::post('/content-creator/upload-featured-image', [ContentCreatorController::class, 'uploadFeaturedImage'])->name('content-creator.upload-featured-image');
    Route::post('/content-creator/generate-media', [ContentCreatorController::class, 'generateMedia'])->middleware('throttle:5,1')->name('content-creator.generate-media');
    Route::post('/content-creator/regenerate', [ContentCreatorController::class, 'regenerate'])->middleware('throttle:10,1')->name('content-creator.regenerate');
    Route::post('/content-creator/publish', [ContentCreatorController::class, 'publish'])->name('content-creator.publish');
    Route::post('/content-creator/{content}/save-visual', [ContentCreatorController::class, 'saveVisual'])->name('content-creator.save-visual');
    Route::delete('/content-creator/{content}', [ContentCreatorController::class, 'destroy'])->name('content-creator.destroy');
    Route::get('/content-creator/{content}/children', [ContentCreatorController::class, 'getChildren'])->name('content-creator.children');
    Route::get('/content-creator/{content}', [ContentCreatorController::class, 'show'])->name('content-creator.show');

    Route::get('/social-planner', [SocialPlannerController::class, 'index'])->name('social-planner.index');
    Route::post('/social-planner/store', [SocialPlannerController::class, 'store'])->name('social-planner.store');
    Route::put('/social-planner/{content}', [SocialPlannerController::class, 'update'])->name('social-planner.update');
    Route::delete('/social-planner/{content}', [SocialPlannerController::class, 'destroy'])->name('social-planner.destroy');
    Route::post('/social-planner/suggestions', [SocialPlannerController::class, 'getSuggestions'])->name('social-planner.suggestions');
    Route::get('/social-planner/facebook-pages', [SocialPlannerController::class, 'getFacebookPages'])->name('social-planner.facebook-pages');
    Route::get('/social/callback/{platform}', [SocialPlannerController::class, 'handleCallback'])->name('social.callback');

    // Knowledge Base (Pro+ Feature)
    Route::middleware('feature:knowledge_base')->group(function () {
        Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
        Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store'])->name('knowledge-base.store');
        Route::delete('/knowledge-base/{asset}', [KnowledgeBaseController::class, 'destroy'])->name('knowledge-base.destroy');
    });

    // AI Agents (Pro+ Feature)
    Route::middleware('feature:ai_agents')->group(function () {
        Route::get('/ai-agents', [AiAgentController::class, 'index'])->name('ai-agents.index');
        Route::post('/ai-agents', [AiAgentController::class, 'store'])->name('ai-agents.store');
        // Static routes MUST come before dynamic {agent} routes
        Route::post('/ai-agents/chat', [AiAgentController::class, 'chat'])->name('ai-agents.chat');
        Route::get('/ai-agents/conversation', [AiAgentController::class, 'getConversation'])->name('ai-agents.conversation');
        Route::post('/ai-agents/conversation/clear', [AiAgentController::class, 'clearConversation'])->name('ai-agents.conversation.clear');
        Route::get('/ai-agents/list', [AiAgentController::class, 'list'])->name('ai-agents.list');
        // Dynamic routes with {agent} parameter
        Route::get('/ai-agents/{agent}', [AiAgentController::class, 'show'])->name('ai-agents.show');
        Route::put('/ai-agents/{agent}', [AiAgentController::class, 'update'])->name('ai-agents.update');
        Route::delete('/ai-agents/{agent}', [AiAgentController::class, 'destroy'])->name('ai-agents.destroy');
    });

    Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [DocumentsController::class, 'show'])->name('documents.show');
    Route::put('/documents/{document}', [DocumentsController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentsController::class, 'destroy'])->name('documents.destroy');

    // Signature Request Routes
    Route::post('/documents/{document}/request-signature', [SignatureRequestController::class, 'send'])->name('documents.request-signature');
    Route::get('/documents/{document}/signatures', [SignatureRequestController::class, 'index'])->name('documents.signatures.index');

    Route::get('/media-registry', [MediaRegistryController::class, 'index'])->name('media-registry.index');
    Route::post('/media-registry', [MediaRegistryController::class, 'store'])->name('media-registry.store');
    Route::delete('/media-registry/{asset}', [MediaRegistryController::class, 'destroy'])->name('media-registry.destroy');
    Route::get('/media-assets', [MediaRegistryController::class, 'getAssets'])->name('media-assets.json');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Help Center
    Route::get('/help-center', [HelpCenterController::class, 'index'])->name('help-center.index');
    Route::get('/help-center/{section}/{article}', [HelpCenterController::class, 'show'])->name('help-center.show');

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
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/toggle-observability', [AdminController::class, 'toggleObservability'])->name('admin.toggle-observability');
        Route::post('/waitlist/{lead}/convert', [AdminController::class, 'convertLead'])->name('admin.waitlist.convert');

        Route::get('/audit', [AuditController::class, 'index'])->name('admin.audit.index');

        Route::get('/tenants', [TenantExplorerController::class, 'index'])->name('admin.tenants.index');
        Route::get('/tenants/{tenant}', [TenantExplorerController::class, 'show'])->name('admin.tenants.show');
        Route::post('/tenants/{tenant}/grant', [TenantExplorerController::class, 'grantTokens'])->name('admin.tenants.grant');
    });

    // God View - Waitlist Management (admin@archit-ai.io only)
    Route::prefix('god-view')->group(function () {
        Route::get('/', [GodViewController::class, 'index'])->name('god-view.index');
        Route::post('/approve/{waitlist}', [GodViewController::class, 'approve'])->name('god-view.approve');
        Route::post('/reject/{waitlist}', [GodViewController::class, 'reject'])->name('god-view.reject');
        Route::delete('/{waitlist}', [GodViewController::class, 'destroy'])->name('god-view.destroy');
        Route::post('/bulk-approve', [GodViewController::class, 'bulkApprove'])->name('god-view.bulk-approve');
    });
});
