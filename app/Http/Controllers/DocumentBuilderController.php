<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use App\Http\Requests\GenerateReportRequest;
use App\Http\Requests\PreviewReportRequest;
use App\Services\ReportService;
use App\Models\Document;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class DocumentBuilderController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected TokenService $tokenService
    ) {}

    public function index(\Illuminate\Http\Request $request): View
    {
        $templateCategories = array_map(fn(ReportTemplate $template) => [
            'id' => $template->value,
            'name' => $template->label(),
            'icon' => $template->icon(),
            'color' => $template->hexColor(),
            'variants' => $template->variants(),
        ], ReportTemplate::cases());

        $selectedResearch = null;
        if ($request->has('research_id')) {
            $selectedResearch = \App\Models\Research::find($request->research_id);
        }

        return view('document-builder.document-builder', compact('templateCategories', 'selectedResearch'));
    }

    public function generate(GenerateReportRequest $request): JsonResponse
    {
        $tokenCost = 30;

        // 1. Check & Consume Tokens
        if (!$this->tokenService->consume(auth()->user(), $tokenCost, 'report_generation', ['topic' => $request->researchTopic ?? $request->analysisType ?? 'Report Generation'])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. Report architecture requires $tokenCost tokens."
            ], 402);
        }

        $data = ReportRequestData::fromArray($request->validated());
        
        try {
            $html = $this->reportService->generateReportHtml($data);

            // Save to Documents table
            $document = Document::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'name' => ($request->researchTopic ?? $request->analysisType ?? 'Generated Report') . ' - ' . now()->format('Y-m-d H:i'),
                'type' => 'HTML',
                'category' => 'Reports',
                'size' => strlen($html),
                'content' => $html,
                'metadata' => [
                    'template' => $request->template,
                    'variant' => $request->variant,
                    'research_topic' => $request->researchTopic
                ]
            ]);
            
            return response()->json([
                'html' => $html,
                'document_id' => $document->id,
                'success' => true
            ]);
        } catch (\Exception $e) {
            // Refund on failure
            $this->tokenService->grant(auth()->user()->tenant, $tokenCost, 'refund_failed_report');
            
            \Illuminate\Support\Facades\Log::error('Report generation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function preview(PreviewReportRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $template = ReportTemplate::from($validated['template']);
        $variant = $validated['variant'] ?? null;
        
        try {
            $html = $this->reportService->generatePreviewHtml($template, $variant);
            return response()->json(['html' => $html, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<div style="padding: 40px; text-align: center; color: #666;"><h2>Template Preview Unavailable</h2><p>The template view is not yet created for this variant.</p></div>',
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}