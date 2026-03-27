<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\DTOs\ReportRequestData;
use App\Services\AI\MiniMaxClient;
use App\Services\BrandResolverService;
use App\Services\Report\SampleContentProvider;
use Illuminate\Support\Facades\Log;

/**
 * Abstract Base Generator
 *
 * Provides common functionality for all document generators.
 * Implements the Template Method pattern where subclasses override
 * specific methods to customize generation behavior.
 */
abstract class BaseGenerator implements DocumentGeneratorInterface
{
    /**
     * Constructor
     *
     * @param  BrandResolverService  $brandResolverService  Service for brand voice and guidelines
     * @param  SampleContentProvider  $sampleContentProvider  Service for fallback sample content
     * @param  MiniMaxClient  $miniMaxClient  AI client for content generation
     */
    public function __construct(
        protected BrandResolverService $brandResolverService,
        protected SampleContentProvider $sampleContentProvider,
        protected MiniMaxClient $miniMaxClient
    ) {}

    /**
     * Generate document content using MiniMax AI API.
     *
     * This is the main template method that orchestrates generation:
     * 1. Build system prompt (template-specific instructions)
     * 2. Build user prompt (context + content)
     * 3. Call MiniMax AI API
     * 4. Sanitize output
     * 5. Return clean HTML or fallback to sample content
     */
    public function generate(ReportRequestData $data, ?string $kbContext = null, ?string $researchData = null): string
    {
        if (! $this->miniMaxClient->isConfigured()) {
            Log::warning('MiniMax API key not configured - using sample content');

            return $this->getFallbackContent($data);
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($data);
            $userPrompt = $this->buildUserPrompt($data, $kbContext, $researchData);

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ];

            $options = [
                'temperature' => $this->getTemperature(),
                'max_tokens' => 4000,
                'timeout' => 120,
            ];

            $response = $this->miniMaxClient->chat($messages, $options);

            if ($response['success']) {
                return $this->sanitizeOutput($response['message']);
            }

            Log::error('MiniMax API error', ['error' => $response['error'] ?? 'Unknown']);
        } catch (\Exception $e) {
            Log::error('MiniMax generation error: '.$e->getMessage());
        }

        // Fallback to sample content
        return $this->getFallbackContent($data);
    }

    /**
     * Build system prompt - must be implemented by subclasses.
     *
     * Each generator defines its own system instructions including:
     * - AI role/persona
     * - Document structure requirements
     * - HTML formatting rules
     * - Content processing guidelines
     */
    abstract public function buildSystemPrompt(ReportRequestData $data): string;

    /**
     * Build user prompt with context and content.
     *
     * Default implementation provides standard context structure.
     * Subclasses can override for template-specific formatting.
     */
    public function buildUserPrompt(ReportRequestData $data, ?string $kbContext = null, ?string $researchData = null): string
    {
        // Ensure null values become empty strings
        $kbContext = $kbContext ?? '';
        $researchData = $researchData ?? '';

        $baseContext = "
            INTERNAL KNOWLEDGE BASE (CONTEXT):
            ---
            {$kbContext}
            ---

            RESEARCH DATA (PRIMARY SOURCE):
            ---
            {$researchData}
            ---
            
            RAW SOURCE CONTENT (SUPPLEMENTARY):
            ---
            {$data->contentData}
            ---
        ";

        return $this->formatUserPrompt($data, $baseContext);
    }

    /**
     * Format user prompt with template-specific instructions.
     *
     * Subclasses override this to add document-specific context.
     */
    abstract protected function formatUserPrompt(ReportRequestData $data, string $baseContext): string;

    /**
     * Get document type identifier.
     * Must be implemented by subclasses.
     */
    abstract public function getDocumentType(): string;

    /**
     * Get AI role description.
     * Must be implemented by subclasses.
     */
    abstract public function getRoleDescription(): string;

    /**
     * Get task description.
     * Must be implemented by subclasses.
     */
    abstract public function getTaskDescription(): string;

    /**
     * Sanitize AI output - removes markdown artifacts.
     *
     * Default implementation handles common cleanup.
     * Subclasses can override for template-specific sanitization.
     */
    public function sanitizeOutput(string $rawOutput): string
    {
        // Remove markdown bold/italic markers
        $content = str_replace(['**', '___'], '', $rawOutput);

        // Remove header hashes if they slipped through
        $content = preg_replace('/^#+\s+/m', '', $content);

        // Remove bullet stars if they slipped through (only if followed by space)
        $content = preg_replace('/^\*\s+/m', '• ', $content);

        // Remove horizontal rule markers
        $content = preg_replace('/^-{3,}$/m', '<hr>', $content);

        // Final trim
        return trim($content);
    }

    /**
     * Check if this generator requires research.
     * Default is false - override in subclasses that need research.
     */
    public function requiresResearch(): bool
    {
        return false;
    }

    /**
     * Check if this generator supports brand instructions.
     * Default is true - override in subclasses that don't need branding.
     */
    public function supportsBrandInstructions(): bool
    {
        return true;
    }

    /**
     * Get OpenAI temperature setting.
     * Default is 0.5 - override in subclasses for different creativity levels.
     */
    protected function getTemperature(): float
    {
        return 0.5;
    }

    /**
     * Build brand instructions using BrandResolverService.
     *
     * @param  int|null  $brandId  Brand identifier
     * @param  string  $templateValue  Template type for brand-specific instructions
     * @return string Brand instructions or empty string if not applicable
     */
    protected function buildBrandInstructions(?int $brandId, string $templateValue): string
    {
        if (! $this->supportsBrandInstructions() || ! $brandId) {
            return '';
        }

        return $this->brandResolverService->buildBrandInstructions($brandId, $templateValue);
    }

    /**
     * Get fallback content when generation fails.
     *
     * @param  ReportRequestData  $data  Request data for fallback selection
     * @return string Sample content HTML
     */
    protected function getFallbackContent(ReportRequestData $data): string
    {
        $overrides = $data->contractDetails ?? [];
        $overrides['recipientName'] = $data->recipientName;
        $overrides['recipientTitle'] = $data->recipientTitle;

        return $this->sampleContentProvider->getContent($data->template, $overrides);
    }

    /**
     * Build core AI directives common to all generators.
     *
     * @param  string  $dataIntegrity  Optional data integrity instructions
     * @return string Core directives text
     */
    protected function buildCoreDirectives(string $dataIntegrity = ''): string
    {
        return "CORE DIRECTIVES:
                                         - **CRITICAL: OUTPUT MUST BE PURE HTML.** Do not use Markdown (no **bold**, no # headers, no -- separators). 
                                         - **CRITICAL: NO SYMBOLS.** Do not use # or * for formatting. Use <h2>, <h3>, <strong>, <ul>, <li> tags.
                                         - **CRITICAL: WRAP ALL TEXT.** Every paragraph must be in a <p> tag. Never return a block of text without tags.
                                         - **CRITICAL: RESTRUCTURE SOURCE CONTENT.** If provided, do not dump the 'Raw Source Content'. You must format it, break it into paragraphs, add headers, and lists.
                                         {$dataIntegrity}
                                         - Do not wrap in <html> or <body> tags.
                                         - Maintain a formal, authoritative, and professional tone.";
    }

    /**
     * Build data integrity instruction for templates that need metric preservation.
     *
     * @return string Data integrity instruction
     */
    protected function buildDataIntegrityInstruction(): string
    {
        return "- **CRITICAL: DATA INTEGRITY.** You must RETAIN all quantitative data, metrics, and specific technical units (e.g., m2, kg, %, $, dates) from the source content. Do not approximate or omit these details.\n";
    }
}
