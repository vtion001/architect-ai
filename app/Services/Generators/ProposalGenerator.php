<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\DTOs\ReportRequestData;

/**
 * Proposal Generator
 * 
 * Specialized generator for professional business proposals with:
 * - Client-focused problem/solution structure
 * - Brand-to-client communication flow
 * - Comprehensive scope of work and deliverables
 * - Timeline and milestone planning
 * - Investment/pricing presentation
 * - Terms and conditions
 * - Research integration for market context
 * 
 * This generator creates persuasive business proposals that
 * position the brand as the solution provider for client needs.
 */
class ProposalGenerator extends BaseGenerator
{
    /**
     * Get document type identifier.
     */
    public function getDocumentType(): string
    {
        return 'proposal';
    }

    /**
     * Get AI role description.
     */
    public function getRoleDescription(): string
    {
        return 'expert proposal writer and sales strategist';
    }

    /**
     * Get task description.
     */
    public function getTaskDescription(): string
    {
        return 'HIGH-IMPACT business proposal';
    }

    /**
     * Build system prompt with proposal structure.
     */
    public function buildSystemPrompt(ReportRequestData $data): string
    {
        $roleDescription = $this->getRoleDescription();
        $taskDescription = $this->getTaskDescription();
        $documentType = $this->getDocumentType();
        
        $dataIntegrity = $this->buildDataIntegrityInstruction();
        $coreDirectives = $this->buildCoreDirectives($dataIntegrity);
        $brandInstructions = $this->buildBrandInstructions($data->brandId, $data->template->value);
        
        // Build proposal structure
        $structure = $this->buildProposalStructure($data);
        
        return "You are an $roleDescription. 
                Your task is to take RAW research data, INTERNAL knowledge base data, and RAW source content and transform them into a $taskDescription.
                
                $coreDirectives
                
                - THE 'RESEARCH DATA' AND 'INTERNAL KNOWLEDGE BASE' ARE YOUR PRIMARY SOURCES OF TRUTH. You must include the facts, figures, and insights from them. DO NOT GENERALIZE.
                - THE 'RESEARCH TOPIC' IS THE MANDATORY THEME. Every section must relate back to the project/service being proposed.
                - GENERATE A DETAILED BUSINESS PROPOSAL. Use a clean, single-column flow.
                - Use <h2> for section titles and <h3> for sub-sections.
                - Use <p>, <ul>, <li>, and <strong> for content.
                - ADVANCED LAYOUTS:
                    * Use <table> for pricing, timelines, or deliverables comparison
                    * Use <div class='callout'>Content</div> for key value propositions or critical points
                    * Use <div class='grid-2'><div>Part 1</div><div>Part 2</div></div> for side-by-side comparisons
                - YOUR PRIMARY JOB IS PERSUASION AND STRUCTURE. Make the proposal professional, compelling, and client-focused.
                
                $brandInstructions
                
                $structure";
    }

    /**
     * Build proposal structure and requirements.
     */
    protected function buildProposalStructure(ReportRequestData $data): string
    {
        $brandData = $this->brandResolverService->resolve($data->brandId);
        $brandName = $brandData['brand']?->name ?? 'Service Provider';

        $structure = "\n[PROPOSAL STRUCTURE AND REQUIREMENTS]\n\n";
        
        $structure .= "COMMUNICATION FLOW:\n";
        $structure .= "   - Proposal FROM: {$brandName} (The Brand/Service Provider)\n";
        $structure .= "   - Proposal FOR: {$data->recipientName} (The Client)\n";
        $structure .= "   - Address content TO the Client, FROM the Brand's perspective\n";
        $structure .= "   - Focus on solving the Client's needs and pain points\n\n";
        
        $structure .= "REQUIRED SECTIONS (in this order):\n\n";
        
        $structure .= "1. EXECUTIVE SUMMARY:\n";
        $structure .= "   - Brief overview of the proposed solution\n";
        $structure .= "   - Key benefits and outcomes for the client\n";
        $structure .= "   - 3-4 paragraphs maximum\n";
        $structure .= "   - Use <div class='callout'> for the main value proposition\n\n";
        
        $structure .= "2. PROBLEM STATEMENT / CLIENT NEEDS:\n";
        $structure .= "   - Identify and articulate the client's challenges\n";
        $structure .= "   - Demonstrate understanding of their situation\n";
        $structure .= "   - Reference industry context from research data\n";
        $structure .= "   - Show empathy and insight\n\n";
        
        $structure .= "3. PROPOSED SOLUTION:\n";
        $structure .= "   - Explain HOW your solution addresses each challenge\n";
        $structure .= "   - Highlight unique approach and methodology\n";
        $structure .= "   - Include relevant case studies or examples if available\n";
        $structure .= "   - Use <ul> lists for key solution components\n\n";
        
        $structure .= "4. SCOPE OF WORK & DELIVERABLES:\n";
        $structure .= "   - Detailed breakdown of what will be delivered\n";
        $structure .= "   - Use <h3> for each major deliverable category\n";
        $structure .= "   - Include specific outputs, features, or services\n";
        $structure .= "   - Use tables if comparing multiple service tiers\n\n";
        
        $structure .= "5. TIMELINE & MILESTONES:\n";
        $structure .= "   - Project phases with estimated durations\n";
        $structure .= "   - Key milestones and checkpoints\n";
        $structure .= "   - Use <table> for timeline visualization:\n";
        $structure .= "     <table>\n";
        $structure .= "       <thead><tr><th>Phase</th><th>Activities</th><th>Duration</th><th>Deliverable</th></tr></thead>\n";
        $structure .= "       <tbody>...</tbody>\n";
        $structure .= "     </table>\n\n";
        
        $structure .= "6. PRICING / INVESTMENT:\n";
        $structure .= "   - Clear pricing structure (hourly, fixed, tiered)\n";
        $structure .= "   - Breakdown of costs by deliverable or phase\n";
        $structure .= "   - Payment terms and schedule\n";
        $structure .= "   - Use <table class='pricing-table'> for professional presentation\n";
        $structure .= "   - Include any discounts, packages, or add-ons\n\n";
        
        $structure .= "7. WHY CHOOSE US / QUALIFICATIONS:\n";
        $structure .= "   - Brief overview of {$brandName}'s expertise\n";
        $structure .= "   - Relevant experience and credentials\n";
        $structure .= "   - Differentiators from competitors\n";
        $structure .= "   - Use bullet points for achievements\n\n";
        
        $structure .= "8. TERMS & CONDITIONS:\n";
        $structure .= "   - Project assumptions and dependencies\n";
        $structure .= "   - Client responsibilities\n";
        $structure .= "   - Change order process\n";
        $structure .= "   - Acceptance criteria\n\n";
        
        $structure .= "9. CALL TO ACTION / NEXT STEPS:\n";
        $structure .= "   - Clear instructions on how to proceed\n";
        $structure .= "   - Proposal validity period\n";
        $structure .= "   - Contact information for questions\n";
        $structure .= "   - Compelling reason to act now\n\n";
        
        $structure .= "TONE AND STYLE:\n";
        $structure .= "   - Professional yet approachable\n";
        $structure .= "   - Client-focused (use 'you' and 'your')\n";
        $structure .= "   - Confident but not arrogant\n";
        $structure .= "   - Solution-oriented and benefit-driven\n";
        $structure .= "   - Use active voice and strong verbs\n\n";
        
        $structure .= "FORMATTING BEST PRACTICES:\n";
        $structure .= "   - Use <h2> for main sections\n";
        $structure .= "   - Use <h3> for subsections\n";
        $structure .= "   - Use <div class='callout'> for key benefits or guarantees\n";
        $structure .= "   - Use tables for structured data (pricing, timeline)\n";
        $structure .= "   - Use bullet lists for features and benefits\n";
        $structure .= "   - Include white space for readability\n\n";
        
        return $structure;
    }

    /**
     * Format user prompt for proposal generation.
     */
    protected function formatUserPrompt(ReportRequestData $data, string $baseContext): string
    {
        $brandData = $this->brandResolverService->resolve($data->brandId);
        $brandName = $brandData['brand']?->name ?? 'Service Provider';

        return "Generate a professional business proposal.
                
                PROPOSAL FROM (The Brand): {$brandName}
                PROPOSAL FOR (The Client): {$data->recipientName}
                CLIENT ROLE: {$data->recipientTitle}
                CLIENT ADDRESS: {$data->companyAddress}
                
                PROJECT/SERVICE: {$data->researchTopic}
                OBJECTIVE: {$data->analysisType}
                FOCUS: {$data->prompt}
                STYLE VARIANT: {$data->variant}
                
                {$baseContext}
                
                CORE MANDATE: 
                - Address the content TO the Client ({$data->recipientName})
                - Write FROM the perspective of {$brandName}
                - Center the 'Problem Statement' and 'Proposed Solution' on the Client's specific needs
                - Use research data to support your solution recommendations
                - Include specific deliverables and timeline from the source content
                - Present pricing/investment professionally
                
                STRUCTURE:
                1. Executive Summary (3-4 paragraphs with key value proposition)
                2. Problem Statement / Client Needs (demonstrate understanding)
                3. Proposed Solution (explain how you solve each challenge)
                4. Scope of Work & Deliverables (detailed breakdown)
                5. Timeline & Milestones (with table visualization)
                6. Pricing / Investment (clear pricing structure with table)
                7. Why Choose Us / Qualifications (brief credentials)
                8. Terms & Conditions (assumptions and dependencies)
                9. Call to Action / Next Steps (clear instructions)
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
    }

    /**
     * Proposals benefit from research for market context and validation.
     */
    public function requiresResearch(): bool
    {
        return true;
    }
}
