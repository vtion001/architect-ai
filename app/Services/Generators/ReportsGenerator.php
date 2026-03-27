<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\DTOs\ReportRequestData;
use App\Enums\ReportTemplate;

/**
 * Reports Generator
 *
 * Specialized generator for all business report types including:
 * - Executive Summary
 * - Market Analysis
 * - Financial Overview
 * - Competitive Intelligence
 * - Infographic / One-Pager
 * - Trend Analysis
 * - Custom Reports
 *
 * This generator creates detailed, research-driven business reports
 * with professional formatting, data visualization, and analytical insights.
 */
class ReportsGenerator extends BaseGenerator
{
    /**
     * Get document type identifier.
     */
    public function getDocumentType(): string
    {
        return 'report';
    }

    /**
     * Get AI role description.
     */
    public function getRoleDescription(): string
    {
        return 'expert business analyst and technical writer';
    }

    /**
     * Get task description.
     */
    public function getTaskDescription(): string
    {
        return 'HIGH-END HTML business report';
    }

    /**
     * Build system prompt with report-specific instructions.
     */
    public function buildSystemPrompt(ReportRequestData $data): string
    {
        $roleDescription = $this->getRoleDescription();
        $taskDescription = $this->getTaskDescription();
        $documentType = $this->getDocumentType();

        $dataIntegrity = $this->buildDataIntegrityInstruction();
        $coreDirectives = $this->buildCoreDirectives($dataIntegrity);
        $brandInstructions = $this->buildBrandInstructions($data->brandId, $data->template->value);

        // Build report-specific structure guidance
        $structureGuidance = $this->buildReportStructureGuidance($data);

        return "You are an $roleDescription. 
                Your task is to take RAW research data, INTERNAL knowledge base data, and RAW source content and transform them into a $taskDescription.
                
                $coreDirectives
                
                - THE 'RESEARCH DATA' AND 'INTERNAL KNOWLEDGE BASE' ARE YOUR PRIMARY SOURCES OF TRUTH. You must include the facts, figures, and insights from them. DO NOT GENERALIZE.
                - THE 'RESEARCH TOPIC' IS THE MANDATORY THEME. Every section must relate back to: {$data->researchTopic}.
                - GENERATE A DETAILED BUSINESS ".strtoupper($documentType).". Use a clean, single-column flow.
                - Use <h2> for section titles and <h3> for sub-sections.
                - Use <p>, <ul>, <li>, and <strong> for content.
                - ADVANCED LAYOUTS:
                    * Use <table> for any data comparisons or metrics found in the research.
                    * Use <div class='callout'>Content</div> for quotes or critical executive findings.
                    * Use <div class='grid-2'><div>Part 1</div><div>Part 2</div></div> sparingly for small side-by-side data points.
                - Maintain a formal, authoritative, and analytical business tone.
                - YOUR PRIMARY JOB IS DESIGN AND STRUCTURE. Ensure the raw data looks like a premium produced $documentType.
                
                $brandInstructions
                
                $structureGuidance";
    }

    /**
     * Build report structure guidance based on template type.
     */
    protected function buildReportStructureGuidance(ReportRequestData $data): string
    {
        $guidance = "\n[REPORT STRUCTURE GUIDANCE]\n\n";

        switch ($data->template) {
            case ReportTemplate::EXECUTIVE_SUMMARY:
                $guidance .= $this->getExecutiveSummaryGuidance();
                break;

            case ReportTemplate::MARKET_ANALYSIS:
                $guidance .= $this->getMarketAnalysisGuidance();
                break;

            case ReportTemplate::FINANCIAL_OVERVIEW:
                $guidance .= $this->getFinancialOverviewGuidance();
                break;

            case ReportTemplate::COMPETITIVE_INTELLIGENCE:
                $guidance .= $this->getCompetitiveIntelligenceGuidance();
                break;

            case ReportTemplate::INFOGRAPHIC:
                $guidance .= $this->getInfographicGuidance();
                break;

            case ReportTemplate::TREND_ANALYSIS:
                $guidance .= $this->getTrendAnalysisGuidance();
                break;

            case ReportTemplate::CUSTOM:
            default:
                $guidance .= $this->getCustomReportGuidance();
                break;
        }

        return $guidance;
    }

    /**
     * Executive Summary guidance.
     */
    protected function getExecutiveSummaryGuidance(): string
    {
        return "EXECUTIVE SUMMARY FORMAT:
                - Start with high-level overview (2-3 paragraphs)
                - Include <div class='callout'> for key takeaways or recommendations
                - Major sections: Overview, Key Findings, Recommendations, Conclusion
                - Use bullet points for findings and recommendations
                - Keep it concise but comprehensive (typically 2-4 pages)
                - Focus on actionable insights for decision-makers\n";
    }

    /**
     * Market Analysis guidance.
     */
    protected function getMarketAnalysisGuidance(): string
    {
        return "MARKET ANALYSIS FORMAT:
                - Section 1: Market Overview (size, growth, trends)
                - Section 2: Market Segmentation (use tables for segment data)
                - Section 3: Customer Analysis (demographics, behavior, needs)
                - Section 4: Competitive Landscape (key players, market share)
                - Section 5: Market Opportunities and Threats
                - Section 6: Future Outlook and Projections
                - Use <table> extensively for market data and statistics
                - Include <div class='callout'> for critical market insights
                - Use <div class='grid-2'> for SWOT or competitive comparisons\n";
    }

    /**
     * Financial Overview guidance.
     */
    protected function getFinancialOverviewGuidance(): string
    {
        return "FINANCIAL OVERVIEW FORMAT:
                - Section 1: Financial Summary (key metrics at a glance)
                - Section 2: Revenue Analysis (sources, trends, breakdown)
                - Section 3: Cost Structure and Profitability
                - Section 4: Financial Ratios and Performance Indicators
                - Section 5: Cash Flow and Liquidity Analysis
                - Section 6: Financial Projections and Forecasts
                - Use <table class='financial-table'> for all financial data
                - Include YoY comparisons and trend analysis
                - Use <div class='callout-critical'> for financial warnings or concerns
                - Preserve ALL numerical data with exact figures\n";
    }

    /**
     * Competitive Intelligence guidance.
     */
    protected function getCompetitiveIntelligenceGuidance(): string
    {
        return "COMPETITIVE INTELLIGENCE FORMAT:
                - Section 1: Competitive Landscape Overview
                - Section 2: Key Competitors Profiles (one section per competitor)
                  * Company Overview
                  * Products/Services
                  * Market Position and Share
                  * Strengths and Weaknesses
                  * Recent Activities and News
                - Section 3: Competitive Comparison Matrix (use table)
                - Section 4: Competitive Advantages and Gaps
                - Section 5: Strategic Recommendations
                - Use <table> for competitor comparison matrices
                - Use <div class='grid-2'> for side-by-side competitor comparisons
                - Include <div class='callout'> for strategic insights\n";
    }

    /**
     * Infographic guidance.
     */
    protected function getInfographicGuidance(): string
    {
        return "INFOGRAPHIC / ONE-PAGER FORMAT:
                - Highly visual, concise, data-driven layout
                - Use <h2> sparingly for major sections only
                - Rely heavily on:
                  * <div class='stat-box'> for key statistics
                  * <div class='callout'> for important facts
                  * Short bullet lists (3-5 items max)
                  * Small tables for data comparison
                - Keep text minimal and impactful
                - Use numbers, percentages, and metrics prominently
                - Section structure: Header Stats → Key Points → Visual Data → Takeaway
                - Maximum 1-2 pages of content\n";
    }

    /**
     * Trend Analysis guidance.
     */
    protected function getTrendAnalysisGuidance(): string
    {
        return "TREND ANALYSIS FORMAT:
                - Section 1: Introduction and Methodology
                - Section 2: Current State Analysis (baseline data)
                - Section 3: Trend Identification (emerging patterns)
                  * For each trend: Description, Data Evidence, Implications
                - Section 4: Trend Drivers and Influencing Factors
                - Section 5: Impact Assessment (short-term and long-term)
                - Section 6: Future Predictions and Scenarios
                - Section 7: Strategic Recommendations
                - Use <table> for trend data and timelines
                - Use <div class='callout'> for key trend insights
                - Include before/after comparisons where applicable\n";
    }

    /**
     * Custom Report guidance.
     */
    protected function getCustomReportGuidance(): string
    {
        return "CUSTOM REPORT FORMAT:
                - Flexible structure based on research topic and content
                - Standard sections: Introduction, Analysis, Findings, Recommendations, Conclusion
                - Adapt structure to fit the specific research topic
                - Use appropriate visualization (tables, callouts, grids) based on data type
                - Maintain professional report formatting throughout
                - Focus on delivering insights relevant to the stated objective\n";
    }

    /**
     * Format user prompt for report generation.
     */
    protected function formatUserPrompt(ReportRequestData $data, string $baseContext): string
    {
        return "Generate a highly detailed business {$data->template->label()} report. 
                
                MANDATORY RESEARCH TOPIC: {$data->researchTopic}
                
                Analysis Case / Objective: {$data->analysisType}.
                Focus / Strategic Mandate: {$data->prompt}.
                Style Variant: {$data->variant}. 
                Recipient: {$data->recipientName} ({$data->recipientTitle}). 
                
                {$baseContext}
                
                INSTRUCTION: Create a comprehensive report specifically about '{$data->researchTopic}'. 
                Use the RESEARCH DATA and INTERNAL KNOWLEDGE BASE provided as your factual base. 
                Build a detailed narrative using the business layout tools (tables, callouts, grids) provided in your system instructions. 
                Do not omit data. 
                Expand the raw research into professional technical analysis. 
                
                **STRICTLY USE HTML TAGS ONLY. NO MARKDOWN SYMBOLS.**";
    }

    /**
     * Reports benefit greatly from deep research.
     */
    public function requiresResearch(): bool
    {
        return true;
    }
}
