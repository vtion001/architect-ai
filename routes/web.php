<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportBuilderController;
use App\Http\Controllers\ResearchEngineController;
use App\Http\Controllers\ContentCreatorController;
use App\Http\Controllers\SocialPlannerController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\AnalyticsController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/report-builder', [ReportBuilderController::class, 'index'])->name('report-builder.index');
Route::post('/report-builder/generate', [ReportBuilderController::class, 'generate'])->name('report-builder.generate');
Route::get('/report-builder/preview', [ReportBuilderController::class, 'preview'])->name('report-builder.preview');

Route::get('/research-engine', [ResearchEngineController::class, 'index'])->name('research-engine.index');
Route::post('/research-engine/start', [ResearchEngineController::class, 'store'])->name('research-engine.start');
Route::get('/research-engine/{research}', [ResearchEngineController::class, 'show'])->name('research-engine.show');
Route::delete('/research-engine/{research}', [ResearchEngineController::class, 'destroy'])->name('research-engine.destroy');
Route::get('/content-creator', [ContentCreatorController::class, 'index'])->name('content-creator.index');
Route::post('/content-creator/generate', [ContentCreatorController::class, 'store'])->name('content-creator.generate');
Route::post('/content-creator/suggestions', [ContentCreatorController::class, 'getSuggestions'])->name('content-creator.suggestions');
Route::post('/content-creator/refine', [ContentCreatorController::class, 'refineContext'])->name('content-creator.refine');
Route::post('/content-creator/upload-media', [ContentCreatorController::class, 'uploadMedia'])->name('content-creator.upload-media');
Route::post('/content-creator/generate-media', [ContentCreatorController::class, 'generateMedia'])->name('content-creator.generate-media');
Route::post('/content-creator/regenerate', [ContentCreatorController::class, 'regenerate'])->name('content-creator.regenerate');
Route::post('/content-creator/publish', [ContentCreatorController::class, 'publish'])->name('content-creator.publish');
Route::get('/content-creator/{content}', [ContentCreatorController::class, 'show'])->name('content-creator.show');
Route::get('/social-planner', [SocialPlannerController::class, 'index'])->name('social-planner.index');
Route::post('/social-planner/suggestions', [SocialPlannerController::class, 'getSuggestions'])->name('social-planner.suggestions');
Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
