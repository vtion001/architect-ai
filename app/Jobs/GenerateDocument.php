<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DTOs\ReportRequestData;
use App\Models\Document;
use App\Models\User;
use App\Services\ReportService;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes

    public $tries = 2; // Retry once on failure

    public function __construct(
        protected Document $document,
        protected User $user,
        protected ReportRequestData $reportData,
        protected int $tokenCost = 30
    ) {}

    public function handle(ReportService $reportService, TokenService $tokenService): void
    {
        // Set Tenant Context for Isolation
        if ($this->user->tenant) {
            app()->instance(\App\Models\Tenant::class, $this->user->tenant);
            session(['current_tenant_id' => $this->user->tenant_id]);
        }

        try {
            Log::info("Job processing report generation for Document ID: {$this->document->id}");

            // Update status to processing
            $this->document->update(['status' => 'processing']);

            // Execute Generation
            $html = $reportService->generateReportHtml($this->reportData);

            // Update document with generated content
            $this->document->update([
                'content' => $html,
                'size' => strlen($html),
                'status' => 'completed',
            ]);

            Log::info("Report generated successfully for Document ID: {$this->document->id}");

        } catch (\Throwable $e) {
            Log::error("Report Generation Job Failed for Document ID {$this->document->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Refund tokens on failure
            $tokenService->grant($this->user->tenant, $this->tokenCost, 'refund_failed_report_generation');

            $this->document->update([
                'status' => 'failed',
                'metadata' => array_merge($this->document->metadata ?? [], [
                    'error' => $e->getMessage(),
                ]),
            ]);

            throw $e;
        }
    }
}
