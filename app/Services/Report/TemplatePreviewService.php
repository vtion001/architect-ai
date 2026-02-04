<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Enums\ReportTemplate;
use App\Models\Brand;
use App\Services\BrandResolverService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

/**
 * Template Preview Service
 * 
 * COMPLETELY ISOLATED preview rendering service for document templates.
 * 
 * This service is DECOUPLED from:
 * - DocumentBuilderController (UI layer)
 * - ReportService (generation layer)
 * - Document Builder Blade views
 * 
 * Changes to any of the above will NOT affect preview rendering.
 * 
 * Architecture:
 * ┌──────────────────────┐     ┌───────────────────────┐
 * │ DocumentBuilderCtrl  │────>│ TemplatePreviewService│
 * └──────────────────────┘     └───────────────────────┘
 *                                        │
 *                              ┌─────────┴─────────┐
 *                              ▼                   ▼
 *                    ┌──────────────┐    ┌──────────────────┐
 *                    │ SampleContent│    │ BrandResolver    │
 *                    │ Provider     │    │ Service          │
 *                    └──────────────┘    └──────────────────┘
 */
class TemplatePreviewService
{
    public function __construct(
        protected SampleContentProvider $sampleContentProvider,
        protected BrandResolverService $brandResolverService
    ) {}

    /**
     * Generate preview HTML for a template.
     * 
     * This method is COMPLETELY ISOLATED from:
     * - Document generation logic
     * - AI content generation
     * - File upload handling
     * - User state management
     * 
     * @param ReportTemplate $template The template type
     * @param string|null $variant The template variant
     * @param string|null $brandId The brand ID for styling
     * @param array $overrides Field overrides for preview
     * @return string Rendered HTML
     */
    public function render(
        ReportTemplate $template, 
        ?string $variant = null, 
        ?string $brandId = null, 
        array $overrides = []
    ): string {
        // Get sample content from dedicated provider
        $sampleContent = $this->sampleContentProvider->getContent($template, $overrides);

        // Resolve brand styling
        $brandData = $this->resolveBrand($brandId);

        // Build preview data context
        $previewData = $this->buildPreviewContext($template, $variant, $brandData, $overrides, $sampleContent);

        // Render template
        return $this->renderTemplate($template, $previewData);
    }

    /**
     * Get a template's raw sample content without rendering.
     * Useful for debugging or content inspection.
     */
    public function getSampleContent(ReportTemplate $template, array $overrides = []): string
    {
        return $this->sampleContentProvider->getContent($template, $overrides);
    }

    /**
     * Get available templates for preview.
     */
    public function getAvailableTemplates(): array
    {
        return array_map(fn(ReportTemplate $t) => [
            'id' => $t->value,
            'name' => $t->label(),
            'icon' => $t->icon(),
            'color' => $t->hexColor(),
            'variants' => $t->variants(),
        ], ReportTemplate::cases());
    }

    /**
     * Validate that a template/variant combination is valid.
     */
    public function isValidCombination(string $templateId, ?string $variantId): bool
    {
        $template = ReportTemplate::tryFrom($templateId);
        if (!$template) {
            return false;
        }

        if (!$variantId) {
            return true;
        }

        $variants = $template->variants();
        return collect($variants)->contains(fn($v) => $v['id'] === $variantId);
    }

    /**
     * Resolve brand data for preview styling.
     */
    protected function resolveBrand(?string $brandId): array
    {
        return $this->brandResolverService->resolve($brandId);
    }

    /**
     * Build the complete preview context.
     */
    protected function buildPreviewContext(
        ReportTemplate $template,
        ?string $variant,
        array $brandData,
        array $overrides,
        string $sampleContent
    ): array {
        // Base context
        $context = [
            'content' => $sampleContent,
            'variant' => $variant,
            'brandColor' => $brandData['primary_color'],
            'logoUrl' => $brandData['logo_url'],
            'profilePhotoUrl' => null,
        ];

        // Add sender/recipient defaults
        $context = array_merge($context, $this->getDefaultRecipientData($overrides));
        $context = array_merge($context, $this->getDefaultContactInfo());
        $context = array_merge($context, $this->getDefaultPersonalInfo());

        // Template-specific context
        $context = $this->addTemplateSpecificContext($template, $context, $overrides);

        return $context;
    }

    /**
     * Get default recipient/sender data with overrides.
     */
    protected function getDefaultRecipientData(array $overrides): array
    {
        return [
            'recipientName' => $overrides['recipientName'] ?? 'Sample Recipient',
            'recipientTitle' => $overrides['recipientTitle'] ?? 'Department Manager',
            'senderName' => $overrides['senderName'] ?? 'Your Name',
            'senderTitle' => $overrides['senderTitle'] ?? 'Professional Title',
            'companyAddress' => $overrides['companyAddress'] ?? '123 Business Rd, Tech City',
            // For contracts, map sender to provider
            'providerName' => $overrides['senderName'] ?? 'Your Name',
            'providerTitle' => $overrides['senderTitle'] ?? 'Professional Title',
        ];
    }

    /**
     * Get default contact info.
     */
    protected function getDefaultContactInfo(): array
    {
        return [
            'contactInfo' => [
                'email' => 'hello@example.com',
                'phone' => '+1 (555) 000-0000',
                'location' => 'City, Country',
                'website' => 'www.portfolio.com',
            ],
        ];
    }

    /**
     * Get default personal info for CV/resume templates.
     */
    protected function getDefaultPersonalInfo(): array
    {
        return [
            'personalInfo' => [],
        ];
    }

    /**
     * Add template-specific context data.
     */
    protected function addTemplateSpecificContext(
        ReportTemplate $template, 
        array $context, 
        array $overrides
    ): array {
        switch ($template) {
            case ReportTemplate::PROPOSAL:
                $context['financials'] = $overrides['financials'] ?? $this->getDefaultFinancials();
                break;
                
            case ReportTemplate::CONTRACT:
                $context['contractDetails'] = $overrides['contractDetails'] ?? $this->getDefaultContractDetails();
                break;
                
            case ReportTemplate::CV_RESUME:
                $context['profilePhotoUrl'] = $overrides['profilePhotoUrl'] ?? null;
                break;
                
            case ReportTemplate::COVER_LETTER:
                $context['targetRole'] = $overrides['targetRole'] ?? 'Software Engineer';
                break;
        }

        return $context;
    }

    /**
     * Get default financials for proposal previews.
     */
    protected function getDefaultFinancials(): array
    {
        return [
            'totalInvestment' => '10,000',
            'currency' => 'USD',
            'timeline' => '4-5 weeks',
            'paymentMilestones' => [
                ['name' => 'Project Kickoff', 'percentage' => 50],
                ['name' => 'Development Complete', 'percentage' => 30],
                ['name' => 'Launch & Final Handoff', 'percentage' => 20],
            ],
        ];
    }

    /**
     * Get default contract details for contract previews.
     */
    protected function getDefaultContractDetails(): array
    {
        return [
            'clientAddress' => '456 Client Ave',
            'clientCity' => 'Business City, ST 12345',
            'clientCountry' => 'United States',
            'clientEmail' => 'client@example.com',
            'startDate' => now()->format('Y-m-d'),
            'duration' => '12 months',
        ];
    }

    /**
     * Render the template view.
     */
    protected function renderTemplate(ReportTemplate $template, array $data): string
    {
        try {
            return View::make($template->view(), $data)->render();
        } catch (\Throwable $e) {
            Log::error('Template preview render failed', [
                'template' => $template->value,
                'error' => $e->getMessage(),
            ]);
            
            return $this->getErrorHtml($template, $e);
        }
    }

    /**
     * Get error HTML when preview fails.
     */
    protected function getErrorHtml(ReportTemplate $template, \Throwable $e): string
    {
        return sprintf(
            '<div style="padding: 40px; text-align: center; color: #666;">
                <h2>Template Preview Unavailable</h2>
                <p>The preview for <strong>%s</strong> could not be rendered.</p>
                <p class="text-xs text-red-400 mt-2">%s</p>
            </div>',
            $template->label(),
            htmlspecialchars($e->getMessage())
        );
    }
}
