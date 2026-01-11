<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use App\Http\Requests\GenerateReportRequest;
use App\Http\Requests\PreviewReportRequest;
use App\Services\ReportService;
use App\Services\PdfToTextService;
use App\Models\Document;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class DocumentBuilderController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected TokenService $tokenService,
        protected PdfToTextService $pdfToTextService
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

        $tenant = app(\App\Models\Tenant::class);
        $brands = $tenant->brands()->get();

        return view('document-builder.document-builder', compact('templateCategories', 'selectedResearch', 'brands'));
    }

    public function parseResume(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,txt,md,docx|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('resume');
            $text = '';

            if ($file->getClientOriginalExtension() === 'pdf') {
                $text = $this->pdfToTextService->extract($file->getPathname());
            } else {
                // Fallback for text-based files
                $text = file_get_contents($file->getPathname());
            }

            if (empty(trim($text))) {
                return response()->json(['success' => false, 'message' => 'Could not extract text from the document.'], 422);
            }

            return response()->json(['success' => true, 'text' => $text]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Parsing failed: ' . $e->getMessage()], 500);
        }
    }

    public function uploadPhoto(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'cv-' . time() . '-' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cv-photos'), $filename);
            
            return response()->json([
                'success' => true,
                'url' => asset('uploads/cv-photos/' . $filename)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Upload failed'], 400);
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

        $validated = $request->validated();

        // Inject Brand Context
        if (!empty($validated['brand_id'])) {
            $brand = \App\Models\Brand::find($validated['brand_id']);
            if ($brand) {
                $brandContext = "\n\n[SYSTEM: STRICT BRAND IDENTITY ENFORCED]\n";
                $brandContext .= "Organization Name: {$brand->name}\n";
                if (!empty($brand->voice_profile['tone'])) $brandContext .= "Tone of Voice: {$brand->voice_profile['tone']}\n";
                if (!empty($brand->contact_info['website'])) $brandContext .= "Website: {$brand->contact_info['website']}\n";
                
                $validated['prompt'] = ($validated['prompt'] ?? '') . $brandContext;
            }
        }

        $data = ReportRequestData::fromArray($validated);
        
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
        $brandId = $validated['brand_id'] ?? null;
        
        try {
            $html = $this->reportService->generatePreviewHtml($template, $variant, $brandId);
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