<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;
use App\Services\Generators\ContractGenerator;
use App\Services\Generators\CoverLetterGenerator;
use App\Services\Generators\CvResumeGenerator;
use App\Services\Generators\DocumentGeneratorInterface;
use App\Services\Generators\ProposalGenerator;
use App\Services\Generators\ReportsGenerator;
use App\Services\Report\SampleContentProvider;
use Illuminate\Support\Facades\View;

/**
 * ReportService - Document Generation Coordinator
 *
 * REFACTORED: Now uses Factory Pattern with specialized generators.
 *
 * This service coordinates document generation by:
 * 1. Accepting document generation requests (ReportRequestData)
 * 2. Performing optional deep research via Gemini (for reports/proposals)
 * 3. Retrieving knowledge base context via RAG system
 * 4. Delegating content generation to specialized generator classes
 * 5. Rendering the final HTML output with proper template variables
 *
 * MODULAR ARCHITECTURE:
 * - CvResumeGenerator: Handles CV/Resume with job tailoring, Core Competencies
 * - CoverLetterGenerator: Handles persuasive 4-part cover letters
 * - ProposalGenerator: Handles business proposals with client focus
 * - ContractGenerator: Handles legal contracts with proper structure
 * - ReportsGenerator: Handles all business reports (Market Analysis, Financial, etc.)
 *
 * Each generator implements DocumentGeneratorInterface with specialized:
 * - System prompts (HOW to format)
 * - User prompts (WHAT to process)
 * - Sanitization logic (cleanup)
 * - Research requirements
 */
class ReportService
{
    public function __construct(
        private readonly ResearchService $researchService,
        private readonly KnowledgeBaseService $knowledgeBaseService,
        private readonly BrandResolverService $brandResolverService,
        private readonly SampleContentProvider $sampleContentProvider
    ) {}

    /**
     * Generate complete HTML report/document.
     *
     * Main entry point for document generation. Generates content using
     * specialized generators, then renders with appropriate template.
     */
    public function generateReportHtml(ReportRequestData $data): string
    {
        $content = $this->generateContent($data);

        // Resolve Brand Logic via centralized service
        $brandData = $this->brandResolverService->resolve($data->brandId);

        return View::make($data->template->view(), [
            'content' => $content,
            'recipientName' => $data->recipientName ?? 'Recipient',
            'recipientTitle' => $data->recipientTitle,
            'variant' => $data->variant,
            'brandColor' => $brandData['primary_color'],
            'logoUrl' => $brandData['logo_url'],
            'profilePhotoUrl' => $data->profilePhotoUrl,
            'contactInfo' => [
                'email' => $data->email,
                'phone' => $data->phone,
                'location' => $data->location,
                'website' => $data->website,
            ],
            'personalInfo' => $data->personalInfo,
            // For Cover Letter, map recipient/sender fields appropriately
            'senderName' => $data->recipientName,
            'senderTitle' => $data->recipientTitle,
            'companyAddress' => $data->companyAddress,
            // For Contract, map sender to provider
            'providerName' => $data->senderName ?? 'Service Provider',
            'providerTitle' => $data->senderTitle ?? 'Professional Title',
        ])->render();
    }

    /**
     * Generate preview HTML with sample data.
     *
     * Used for template previewing before actual generation.
     */
    public function generatePreviewHtml(ReportTemplate $template, ?string $variant = null, ?string $brandId = null, array $overrides = []): string
    {
        // Use centralized sample content provider
        $sampleContent = $this->sampleContentProvider->getContent($template, $overrides);

        // Resolve Brand Logic via centralized service
        $brandData = $this->brandResolverService->resolve($brandId);

        return View::make($template->view(), [
            'content' => $sampleContent,
            'recipientName' => 'Sample Recipient',
            'recipientTitle' => 'Department Manager',
            'senderName' => 'Your Name',
            'senderTitle' => 'Professional Title',
            'companyAddress' => '123 Business Rd, Tech City',
            'variant' => $variant,
            'brandColor' => $brandData['primary_color'],
            'logoUrl' => $brandData['logo_url'],
            'profilePhotoUrl' => null,
            'contactInfo' => [
                'email' => 'hello@example.com',
                'phone' => '+1 (555) 000-0000',
                'location' => 'City, Country',
                'website' => 'www.portfolio.com',
            ],
            'personalInfo' => [],
            'providerName' => 'Service Provider Name',
            'providerTitle' => 'Professional Consultant',
        ])->render();
    }

    /**
     * Generate document content using specialized generators (REFACTORED).
     *
     * This method implements the Factory Pattern:
     * 1. Retrieves knowledge base context (RAG)
     * 2. Performs deep research if generator requires it (Gemini)
     * 3. Creates appropriate generator for document type
     * 4. Delegates content generation to specialized generator
     *
     * Previously: 753-line monolithic method with all template logic
     * Now: Clean 30-line coordinator that delegates to specialized generators
     */
    private function generateContent(ReportRequestData $data): string
    {
        // RAG: Retrieve relevant context from knowledge base
        $kbContext = null;
        if ($data->researchTopic) {
            $kbContext = $this->knowledgeBaseService->getContext(
                $data->researchTopic.' '.$data->prompt
            );
        }

        // Create specialized generator for this document type
        $generator = $this->createGenerator($data->template);

        // Deep Research: Use Gemini for comprehensive research (ONLY if generator requires it)
        // This optimization saves API calls for documents that don't benefit from research
        $researchData = null;
        if ($data->researchTopic && $generator->requiresResearch()) {
            $researchData = $this->researchService->performResearch(
                $data->researchTopic,
                $data->analysisType ?? 'comprehensive analysis'
            );
        }

        // Delegate content generation to specialized generator
        return $generator->generate($data, $kbContext, $researchData);
    }

    /**
     * Factory Method: Create appropriate generator based on template type.
     *
     * DESIGN PATTERN: Factory Pattern
     *
     * This method encapsulates generator instantiation logic.
     * Each document type gets its own specialized generator with:
     * - Template-specific prompt engineering
     * - Custom HTML structure requirements
     * - Dedicated content processing rules
     * - Specialized sanitization logic
     *
     * Benefits:
     * - Single Responsibility: Each generator handles one document type
     * - Open/Closed: Easy to add new generators without modifying ReportService
     * - Testability: Generators can be unit tested independently
     * - Maintainability: Template logic is isolated and discoverable
     */
    private function createGenerator(ReportTemplate $template): DocumentGeneratorInterface
    {
        return match ($template) {
            // CV/Resume: Job tailoring, Core Competencies, Zero data loss
            ReportTemplate::CV_RESUME => new CvResumeGenerator(
                $this->brandResolverService,
                $this->sampleContentProvider
            ),

            // Cover Letter: 4-part story structure, persuasive writing
            ReportTemplate::COVER_LETTER => new CoverLetterGenerator(
                $this->brandResolverService,
                $this->sampleContentProvider
            ),

            // Proposal: Client-focused, solution-oriented, pricing/timeline
            ReportTemplate::PROPOSAL => new ProposalGenerator(
                $this->brandResolverService,
                $this->sampleContentProvider
            ),

            // Contract: Legal structure, comprehensive articles, formal language
            ReportTemplate::CONTRACT => new ContractGenerator(
                $this->brandResolverService,
                $this->sampleContentProvider
            ),

            // All Report Types: Research-driven, analytical, data visualization
            ReportTemplate::EXECUTIVE_SUMMARY,
            ReportTemplate::MARKET_ANALYSIS,
            ReportTemplate::FINANCIAL_OVERVIEW,
            ReportTemplate::COMPETITIVE_INTELLIGENCE,
            ReportTemplate::INFOGRAPHIC,
            ReportTemplate::TREND_ANALYSIS,
            ReportTemplate::CUSTOM => new ReportsGenerator(
                $this->brandResolverService,
                $this->sampleContentProvider
            ),
        };
    }

    /**
     * @deprecated Use SampleContentProvider::getContent() directly
     */
    private function getDummyContent(): string
    {
        return $this->sampleContentProvider->getContent(ReportTemplate::EXECUTIVE_SUMMARY);
    }

    /**
     * @deprecated Use SampleContentProvider::getContent() directly
     */
    private function getSampleContentForTemplate(ReportTemplate $template, array $overrides = []): string
    {
        return $this->sampleContentProvider->getContent($template, $overrides);
    }

    /**
     * @deprecated Use SampleContentProvider::getContent() directly
     */
    private function getSampleContent(): string
    {
        return $this->sampleContentProvider->getContent(ReportTemplate::EXECUTIVE_SUMMARY);
    }

    /**
     * @deprecated Use KnowledgeBaseService::getContext() directly
     */
    protected function getKnowledgeBaseContext(string $query): ?string
    {
        return $this->knowledgeBaseService->getContext($query);
    }
}
