<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use App\Http\Requests\GenerateReportRequest;
use App\Http\Requests\PreviewReportRequest;
use App\Services\ReportService;
use App\Services\Report\TemplatePreviewService;
use App\Services\ResumeParserService;
use App\Services\CoverLetterDraftService;
use App\Services\TokenService;
use App\Models\Document;
use App\Models\Brand;
use App\Models\Research;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Document Builder Controller
 * 
 * Handles document generation and related file operations.
 * 
 * ARCHITECTURE NOTE: Preview rendering is DECOUPLED via TemplatePreviewService.
 * This ensures changes to this controller do NOT affect preview rendering.
 * 
 * Service delegation:
 * - TemplatePreviewService: ISOLATED preview rendering (decoupled)
 * - ResumeParserService: Resume parsing and AI extraction
 * - CoverLetterDraftService: AI-powered cover letter drafting
 * - ReportService: AI document generation
 */
class DocumentBuilderController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected TemplatePreviewService $previewService,
        protected TokenService $tokenService,
        protected ResumeParserService $resumeParser,
        protected CoverLetterDraftService $coverLetterDraft
    ) {}

    /**
     * Display the document builder interface.
     */
    public function index(Request $request): View
    {
        $templateCategories = $this->getTemplateCategories();
        $selectedResearch = $this->getSelectedResearch($request);
        $brands = $this->getTenantBrands();

        return view('document-builder.document-builder', compact(
            'templateCategories', 
            'selectedResearch', 
            'brands'
        ));
    }

    /**
     * Parse uploaded resume and extract candidate data.
     */
    public function parseResume(Request $request): JsonResponse
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,txt,md,docx|max:5120',
        ]);

        $result = $this->resumeParser->parse($request->file('resume'));

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Upload profile photo for CV/resume.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if (!$request->hasFile('photo')) {
            return response()->json(['success' => false, 'message' => 'Upload failed'], 400);
        }

        $file = $request->file('photo');
        $filename = 'cv-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/cv-photos'), $filename);
        
        return response()->json([
            'success' => true,
            'url' => asset('uploads/cv-photos/' . $filename)
        ]);
    }

    /**
     * Generate a document using AI.
     */
    public function generate(GenerateReportRequest $request): JsonResponse
    {
        try {
            $tokenCost = 30;

            // Check & consume tokens
            if (!$this->tokenService->consume(
                auth()->user(), 
                $tokenCost, 
                'report_generation', 
                ['topic' => $request->researchTopic ?? $request->analysisType ?? 'Report Generation']
            )) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient tokens. Report architecture requires $tokenCost tokens."
                ], 402);
            }

            $validated = $request->validated();
            $validated = $this->injectBrandContext($validated);
            
            $data = ReportRequestData::fromArray($validated);
            $document = $this->createPendingDocument($request, $validated);
            
            // Dispatch background job
            \App\Jobs\GenerateDocument::dispatch($document, auth()->user(), $data, $tokenCost);
            
            return response()->json([
                'success' => true,
                'status' => 'processing',
                'document_id' => $document->id,
                'message' => 'Document generation started. You can navigate away - we\'ll save it to your Documents when ready.'
            ]);

        } catch (\Exception $e) {
            Log::error('Report generation dispatch failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate preview HTML for a template.
     * 
     * DECOUPLED: Uses TemplatePreviewService which is completely
     * isolated from document generation logic.
     */
    public function preview(PreviewReportRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $template = ReportTemplate::from($validated['template']);
        $variant = $validated['variant'] ?? null;
        $brandId = $validated['brand_id'] ?? null;
        
        try {
            $overrides = $this->buildPreviewOverrides($validated);
            
            // Use isolated preview service (decoupled from generation)
            $html = $this->previewService->render($template, $variant, $brandId, $overrides);
            
            return response()->json(['html' => $html, 'success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'html' => $this->getPreviewErrorHtml($e),
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Draft a cover letter using AI.
     */
    public function draftCoverLetter(Request $request): JsonResponse
    {
        $request->validate([
            'target_role' => 'required|string',
            'source_content' => 'required|string',
        ]);

        $result = $this->coverLetterDraft->draft(
            $request->target_role,
            $request->source_content
        );

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }

    // =========================================================================
    // Private Helper Methods
    // =========================================================================

    /**
     * Get template categories for the UI.
     */
    private function getTemplateCategories(): array
    {
        return array_map(fn(ReportTemplate $template) => [
            'id' => $template->value,
            'name' => $template->label(),
            'thumbnail' => $template->thumbnail(),
            'icon' => $template->icon(),
            'color' => $template->hexColor(),
            'variants' => $template->variants(),
        ], ReportTemplate::cases());
    }

    /**
     * Get selected research if provided.
     */
    private function getSelectedResearch(Request $request): ?Research
    {
        if (!$request->has('research_id')) {
            return null;
        }
        
        return Research::find($request->research_id);
    }

    /**
     * Get brands for the current tenant.
     */
    private function getTenantBrands()
    {
        $tenant = app(Tenant::class);
        return $tenant->brands()->get();
    }

    /**
     * Inject brand context into validated data.
     */
    private function injectBrandContext(array $validated): array
    {
        if (empty($validated['brand_id'])) {
            return $validated;
        }

        $brand = Brand::find($validated['brand_id']);
        if (!$brand) {
            return $validated;
        }

        $brandContext = "\n\n[SYSTEM: STRICT BRAND IDENTITY ENFORCED]\n";
        $brandContext .= "Organization Name: {$brand->name}\n";
        
        if (!empty($brand->voice_profile['tone'])) {
            $brandContext .= "Tone of Voice: {$brand->voice_profile['tone']}\n";
        }
        if (!empty($brand->contact_info['website'])) {
            $brandContext .= "Website: {$brand->contact_info['website']}\n";
        }
        
        $validated['prompt'] = ($validated['prompt'] ?? '') . $brandContext;
        
        return $validated;
    }

    /**
     * Create a pending document for background processing.
     */
    private function createPendingDocument(GenerateReportRequest $request, array $validated): Document
    {
        $templateLabel = ReportTemplate::tryFrom($request->template)?->label() ?? 'Report';
        $baseName = $this->generateDocumentName($request, $templateLabel);

        return Document::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'name' => Str::limit($baseName, 150) . ' (' . now()->format('M d, Y') . ')',
            'type' => 'HTML',
            'category' => 'Reports',
            'status' => 'pending',
            'content' => '',
            'metadata' => [
                'template' => $request->template,
                'variant' => $request->variant,
                'research_topic' => $request->researchTopic,
                'profile_photo_url' => $validated['profilePhotoUrl'] ?? null 
            ]
        ]);
    }

    /**
     * Generate document name based on input.
     */
    private function generateDocumentName(GenerateReportRequest $request, string $templateLabel): string
    {
        if (!empty($request->recipientName)) {
            return $request->recipientName . ' - ' . $templateLabel;
        }
        
        if (!empty($request->targetRole)) {
            return $request->targetRole . ' - ' . $templateLabel;
        }
        
        if (!empty($request->researchTopic)) {
            return $request->researchTopic . ' - ' . $templateLabel;
        }
        
        return $request->analysisType ?? 'Generated Document';
    }

    /**
     * Build overrides array for preview.
     */
    private function buildPreviewOverrides(array $validated): array
    {
        $overrides = $validated['contractDetails'] ?? [];
        
        if (!empty($validated['recipientName'])) {
            $overrides['recipientName'] = $validated['recipientName'];
        }
        if (!empty($validated['recipientTitle'])) {
            $overrides['recipientTitle'] = $validated['recipientTitle'];
        }
        
        return $overrides;
    }

    /**
     * Get error HTML for preview failures.
     */
    private function getPreviewErrorHtml(\Throwable $e): string
    {
        return '<div style="padding: 40px; text-align: center; color: #666;">'
            . '<h2>Template Preview Unavailable</h2>'
            . '<p>The template view is not yet created for this variant.</p>'
            . '<p class="text-xs text-red-400 mt-2">' . $e->getMessage() . '</p>'
            . '</div>';
    }
}