<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use App\Http\Requests\GenerateReportRequest;
use App\Http\Requests\PreviewReportRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReportBuilderController extends Controller
{
    public function __construct(
        protected ReportService $reportService
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

        return view('report-builder.index', compact('templateCategories', 'selectedResearch'));
    }

    public function generate(GenerateReportRequest $request): JsonResponse
    {
        $data = ReportRequestData::fromArray($request->validated());
        $html = $this->reportService->generateReportHtml($data);
        
        return response()->json(['html' => $html]);
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
