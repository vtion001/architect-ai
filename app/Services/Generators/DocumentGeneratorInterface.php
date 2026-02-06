<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\DTOs\ReportRequestData;

/**
 * Interface DocumentGeneratorInterface
 * 
 * Defines the contract for all document generators in the system.
 * Each document type (CV/Resume, Cover Letter, Proposal, Contract, Reports)
 * must implement this interface to provide consistent generation behavior.
 * 
 * This follows the Strategy Pattern, allowing ReportService to delegate
 * generation logic to specialized generators.
 */
interface DocumentGeneratorInterface
{
    /**
     * Generate the complete HTML content for the document.
     * 
     * This method orchestrates the entire generation process:
     * 1. Builds the AI prompt with template-specific instructions
     * 2. Calls OpenAI API with the prompt
     * 3. Processes and sanitizes the response
     * 4. Returns clean HTML content
     * 
     * @param ReportRequestData $data Complete request data including content, metadata, and settings
     * @param string|null $kbContext Knowledge base context from RAG system (optional)
     * @param string|null $researchData Deep research data from Gemini (optional)
     * @return string Generated HTML content ready for template rendering
     */
    public function generate(ReportRequestData $data, ?string $kbContext = null, ?string $researchData = null): string;

    /**
     * Build the AI system prompt with template-specific instructions.
     * 
     * This method defines HOW the AI should format and structure the document.
     * Each generator provides its own specialized instructions for:
     * - Role definition (e.g., "expert career coach", "legal drafter")
     * - Output structure and required sections
     * - HTML formatting rules and CSS classes to use
     * - Content processing rules (e.g., data integrity, tailoring)
     * 
     * @param ReportRequestData $data Request data for context-specific instructions
     * @return string Complete system prompt for OpenAI
     */
    public function buildSystemPrompt(ReportRequestData $data): string;

    /**
     * Build the user prompt with context and source content.
     * 
     * This method defines WHAT content the AI should process.
     * It assembles:
     * - Knowledge base context (internal company/domain knowledge)
     * - Research data (external intelligence gathered by Gemini)
     * - Raw source content (user-provided data)
     * - Specific instructions for content processing
     * 
     * @param ReportRequestData $data Request data containing source content
     * @param string|null $kbContext Knowledge base context
     * @param string|null $researchData Research data
     * @return string Complete user prompt for OpenAI
     */
    public function buildUserPrompt(ReportRequestData $data, ?string $kbContext = null, ?string $researchData = null): string;

    /**
     * Get the document type identifier.
     * 
     * Returns a human-readable document type string used in prompts
     * and logging (e.g., "resume", "contract", "proposal", "report").
     * 
     * @return string Document type identifier
     */
    public function getDocumentType(): string;

    /**
     * Get the role description for the AI.
     * 
     * Returns the AI persona to adopt for this document type
     * (e.g., "expert career coach and resume writer",
     * "expert legal drafter and contract attorney").
     * 
     * @return string Role description for AI persona
     */
    public function getRoleDescription(): string;

    /**
     * Get the task description for the AI.
     * 
     * Returns the high-level objective for the AI
     * (e.g., "PROFESSIONAL ATS-FRIENDLY resume",
     * "COMPREHENSIVE, LEGALLY SOUND business contract").
     * 
     * @return string Task description for AI objective
     */
    public function getTaskDescription(): string;

    /**
     * Process and sanitize the AI-generated output.
     * 
     * This method cleans up the raw AI response:
     * - Removes markdown artifacts (**, ##, etc.)
     * - Ensures proper HTML formatting
     * - Extracts special sections (e.g., tailoring reports)
     * - Applies template-specific post-processing
     * 
     * @param string $rawOutput Raw output from OpenAI API
     * @return string Sanitized HTML content
     */
    public function sanitizeOutput(string $rawOutput): string;

    /**
     * Check if this generator requires research data.
     * 
     * Some generators (Reports, Proposals) benefit from deep research,
     * while others (CV/Resume, Cover Letter) rely primarily on source content.
     * 
     * @return bool True if research is recommended for this document type
     */
    public function requiresResearch(): bool;

    /**
     * Check if this generator supports brand instructions.
     * 
     * Most generators can incorporate brand voice and guidelines,
     * but some (like standard contracts) may not need brand customization.
     * 
     * @return bool True if brand instructions should be included
     */
    public function supportsBrandInstructions(): bool;
}
