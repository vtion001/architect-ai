<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ReportTemplate;
use App\Http\Requests\PreviewReportRequest;
use App\Services\Report\TemplatePreviewService;
use Illuminate\Http\JsonResponse;

/**
 * Template Preview Controller
 * 
 * ISOLATED controller for template preview rendering.
 * 
 * This controller is COMPLETELY SEPARATE from:
 * - DocumentBuilderController
 * - Document generation logic
 * - File uploads and parsing
 * 
 * This ensures that changes to the Document Builder do NOT
 * affect preview rendering and vice versa.
 * 
 * Routes:
 * - POST /api/templates/preview    - Render template preview
 * - GET  /api/templates            - List available templates
 * - GET  /api/templates/{id}       - Get template info
 */
class TemplatePreviewController extends Controller
{
    public function __construct(
        protected TemplatePreviewService $previewService
    ) {}

    /**
     * Render a template preview.
     * 
     * @param PreviewReportRequest $request
     * @return JsonResponse
     */
    public function preview(PreviewReportRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $template = ReportTemplate::tryFrom($validated['template']);
        if (!$template) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid template type',
            ], 422);
        }

        $variant = $validated['variant'] ?? null;
        $brandId = $validated['brand_id'] ?? null;
        $overrides = $this->extractOverrides($validated);

        try {
            $html = $this->previewService->render($template, $variant, $brandId, $overrides);
            
            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'html' => $this->getFallbackHtml($template),
            ]);
        }
    }

    /**
     * List all available templates.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'templates' => $this->previewService->getAvailableTemplates(),
        ]);
    }

    /**
     * Get a specific template's info.
     * 
     * @param string $templateId
     * @return JsonResponse
     */
    public function show(string $templateId): JsonResponse
    {
        $template = ReportTemplate::tryFrom($templateId);
        
        if (!$template) {
            return response()->json([
                'success' => false,
                'error' => 'Template not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'template' => [
                'id' => $template->value,
                'name' => $template->label(),
                'icon' => $template->icon(),
                'color' => $template->hexColor(),
                'variants' => $template->variants(),
            ],
        ]);
    }

    /**
     * Validate a template/variant combination.
     * 
     * @param string $templateId
     * @param string|null $variantId
     * @return JsonResponse
     */
    public function validateCombination(string $templateId, ?string $variantId = null): JsonResponse
    {
        $isValid = $this->previewService->isValidCombination($templateId, $variantId);
        
        return response()->json([
            'success' => true,
            'valid' => $isValid,
        ]);
    }

    /**
     * Extract field overrides from validated request data.
     */
    protected function extractOverrides(array $validated): array
    {
        $overrides = [];

        // Recipient/Sender info
        if (!empty($validated['recipientName'])) {
            $overrides['recipientName'] = $validated['recipientName'];
        }
        if (!empty($validated['recipientTitle'])) {
            $overrides['recipientTitle'] = $validated['recipientTitle'];
        }
        if (!empty($validated['senderName'])) {
            $overrides['senderName'] = $validated['senderName'];
        }
        if (!empty($validated['senderTitle'])) {
            $overrides['senderTitle'] = $validated['senderTitle'];
        }

        // Contract details
        if (!empty($validated['contractDetails'])) {
            $overrides['contractDetails'] = $validated['contractDetails'];
        }

        // Financials
        if (!empty($validated['financials'])) {
            $overrides['financials'] = $validated['financials'];
        }

        return $overrides;
    }

    /**
     * Get fallback HTML when preview fails.
     */
    protected function getFallbackHtml(?ReportTemplate $template): string
    {
        $name = $template ? $template->label() : 'Template';
        
        return sprintf(
            '<div style="padding: 60px; text-align: center; color: #888; font-family: system-ui;">
                <div style="font-size: 48px; margin-bottom: 20px;">📄</div>
                <h2 style="color: #333; margin-bottom: 10px;">Preview Unavailable</h2>
                <p>The <strong>%s</strong> preview could not be rendered at this time.</p>
                <p style="font-size: 12px; color: #aaa; margin-top: 20px;">Please try again or select a different template.</p>
            </div>',
            htmlspecialchars($name)
        );
    }
}
