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
Route::get('/content-creator', [ContentCreatorController::class, 'index'])->name('content-creator.index');
Route::get('/social-planner', [SocialPlannerController::class, 'index'])->name('social-planner.index');
Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
