<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportTemplate: string
{
    case EXECUTIVE_SUMMARY = 'executive-summary';
    case MARKET_ANALYSIS = 'market-analysis';
    case FINANCIAL_OVERVIEW = 'financial-overview';
    case COMPETITIVE_INTELLIGENCE = 'competitive-intelligence';
    case INFOGRAPHIC = 'infographic';
    case TREND_ANALYSIS = 'trend-analysis';
    case PROPOSAL = 'proposal';
    case CONTRACT = 'contract';
    case CV_RESUME = 'cv-resume';
    case COVER_LETTER = 'cover-letter';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => 'Executive Summary',
            self::MARKET_ANALYSIS => 'Market Analysis',
            self::FINANCIAL_OVERVIEW => 'Financial Overview',
            self::COMPETITIVE_INTELLIGENCE => 'Competitive Intel',
            self::INFOGRAPHIC => 'Infographic / One-Pager',
            self::TREND_ANALYSIS => 'Trend Analysis',
            self::PROPOSAL => 'Business Proposal',
            self::CONTRACT => 'Legal Contract',
            self::CV_RESUME => 'CV / Resume',
            self::COVER_LETTER => 'Cover Letter',
            self::CUSTOM => 'Custom Template',
        };
    }

    public function view(): string
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => 'reports.executive-summary',
            self::MARKET_ANALYSIS => 'reports.market-analysis',
            self::FINANCIAL_OVERVIEW => 'reports.financial-overview',
            self::COMPETITIVE_INTELLIGENCE => 'reports.competitive-intelligence',
            self::INFOGRAPHIC => 'reports.infographic',
            self::TREND_ANALYSIS => 'reports.trend-analysis',
            self::PROPOSAL => 'reports.proposal',
            self::CONTRACT => 'reports.contract',
            self::CV_RESUME => 'reports.cv-resume',
            self::COVER_LETTER => 'reports.cover-letter',
            self::CUSTOM => 'reports.custom',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => 'briefcase',
            self::MARKET_ANALYSIS => 'bar-chart-3',
            self::FINANCIAL_OVERVIEW => 'trending-up',
            self::COMPETITIVE_INTELLIGENCE => 'shield-alert',
            self::INFOGRAPHIC => 'layout-template',
            self::TREND_ANALYSIS => 'layers',
            self::PROPOSAL => 'file-text',
            self::CONTRACT => 'scale',
            self::CV_RESUME => 'user-square',
            self::COVER_LETTER => 'mail',
            self::CUSTOM => 'terminal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => 'text-blue-500',
            self::MARKET_ANALYSIS => 'text-emerald-500',
            self::FINANCIAL_OVERVIEW => 'text-amber-500',
            self::COMPETITIVE_INTELLIGENCE => 'text-cyan-500',
            self::INFOGRAPHIC => 'text-pink-500',
            self::TREND_ANALYSIS => 'text-slate-500',
            self::PROPOSAL => 'text-orange-500',
            self::CONTRACT => 'text-slate-700',
            self::CV_RESUME => 'text-violet-500',
            self::COVER_LETTER => 'text-teal-500',
            self::CUSTOM => 'text-purple-500',
        };
    }

    public function hexColor(): string
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => '#1e3a8a',
            self::MARKET_ANALYSIS => '#059669',
            self::FINANCIAL_OVERVIEW => '#b45309',
            self::COMPETITIVE_INTELLIGENCE => '#0891b2',
            self::INFOGRAPHIC => '#ec4899',
            self::TREND_ANALYSIS => '#475569',
            self::PROPOSAL => '#f97316',
            self::CONTRACT => '#334155',
            self::CV_RESUME => '#8b5cf6',
            self::COVER_LETTER => '#14b8a6',
            self::CUSTOM => '#7c3aed',
        };
    }

    public function thumbnail(): string
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => 'executive',
            self::MARKET_ANALYSIS => 'market-analysis',
            self::FINANCIAL_OVERVIEW => 'financial',
            self::COMPETITIVE_INTELLIGENCE => 'competitive-intelligence',
            self::INFOGRAPHIC => 'infographic',
            self::TREND_ANALYSIS => 'trend-analysis',
            self::PROPOSAL => 'proposal',
            self::CONTRACT => 'contract',
            self::CV_RESUME => 'cv',
            self::COVER_LETTER => 'cover-letter',
            self::CUSTOM => 'custom',
        };
    }

    /**
     * @return array<int, array{id: string, name: string, description: string, previewImage: string, tags: string[]}>
     */
    public function variants(): array
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => [
                ['id' => 'exec-corporate', 'name' => 'Corporate Profile', 'description' => 'Professional company overview with key metrics and milestones', 'previewImage' => 'executive', 'tags' => ['Professional', 'Metrics']],
                ['id' => 'exec-minimal', 'name' => 'Minimal Executive', 'description' => 'Clean, minimalist layout focused on key insights', 'previewImage' => 'executive', 'tags' => ['Minimal', 'Clean']],
                ['id' => 'exec-detailed', 'name' => 'Comprehensive Report', 'description' => 'Detailed analysis with sections for findings and actions', 'previewImage' => 'executive', 'tags' => ['Detailed', 'Charts']],
            ],
            self::MARKET_ANALYSIS => [
                ['id' => 'market-overview', 'name' => 'Market Overview', 'description' => 'Comprehensive market landscape with trends and threats', 'previewImage' => 'market-analysis', 'tags' => ['Trends', 'SWOT']],
                ['id' => 'market-competitive', 'name' => 'Competitive Landscape', 'description' => 'Focus on competitor benchmarks', 'previewImage' => 'market-analysis', 'tags' => ['Competitors', 'Benchmarks']],
                ['id' => 'market-segment', 'name' => 'Segment Analysis', 'description' => 'Deep dive into market segments', 'previewImage' => 'market-analysis', 'tags' => ['Segments', 'Demographics']],
            ],
            self::FINANCIAL_OVERVIEW => [
                ['id' => 'fin-dashboard', 'name' => 'Financial Dashboard', 'description' => 'Key financial metrics with performance indicators', 'previewImage' => 'financial', 'tags' => ['Metrics', 'Ratios']],
                ['id' => 'fin-forecast', 'name' => 'Forecast Report', 'description' => 'Financial projections and scenarios', 'previewImage' => 'financial', 'tags' => ['Projections', 'Scenarios']],
                ['id' => 'fin-quarterly', 'name' => 'Quarterly Review', 'description' => 'Period-over-period comparison', 'previewImage' => 'financial', 'tags' => ['Quarterly', 'Variance']],
            ],
            self::COMPETITIVE_INTELLIGENCE => [
                ['id' => 'intel-battlecard', 'name' => 'Competitor Battlecard', 'description' => 'Quick-reference competitive intelligence', 'previewImage' => 'competitive-intelligence', 'tags' => ['Quick Ref', 'SWOT']],
                ['id' => 'intel-deep', 'name' => 'Deep Dive Analysis', 'description' => 'Comprehensive competitor profiling', 'previewImage' => 'competitive-intelligence', 'tags' => ['Strategy', 'Detailed']],
            ],
            self::INFOGRAPHIC => [
                ['id' => 'infographic-corporate', 'name' => 'Corporate Profile', 'description' => 'Professional company overview with key metrics and milestones', 'previewImage' => 'infographic', 'tags' => ['Professional', 'Metrics']],
                ['id' => 'infographic-startup', 'name' => 'Startup Pitch', 'description' => 'Visual pitch deck style with problem/solution fit', 'previewImage' => 'infographic', 'tags' => ['Pitch', 'Visual']],
            ],
            self::TREND_ANALYSIS => [
                ['id' => 'trend-timeline', 'name' => 'Trend Timeline', 'description' => 'Historical analysis with future projections', 'previewImage' => 'trend-analysis', 'tags' => ['Historical', 'Timeline']],
                ['id' => 'trend-industry', 'name' => 'Industry Trends', 'description' => 'Sector-specific trends with implications', 'previewImage' => 'trend-analysis', 'tags' => ['Industry', 'Strategic']],
            ],
            self::PROPOSAL => [
                ['id' => 'proposal-standard', 'name' => 'Standard Proposal', 'description' => 'Clear offer with deliverables and pricing', 'previewImage' => 'proposal', 'tags' => ['Offer', 'Pricing']],
                ['id' => 'proposal-modern', 'name' => 'Modern Pitch', 'description' => 'Visually engaging proposal for creative services', 'previewImage' => 'strategy', 'tags' => ['Creative', 'Visual']],
            ],
            self::CONTRACT => [
                ['id' => 'contract-service', 'name' => 'Service Agreement', 'description' => 'Comprehensive international services contract with payment terms', 'previewImage' => 'contract', 'tags' => ['Legal', 'SLA']],
                ['id' => 'contract-nda', 'name' => 'NDA', 'description' => 'Non-disclosure and confidentiality agreement', 'previewImage' => 'audit', 'tags' => ['Legal', 'Protection']],
                ['id' => 'contract-employment', 'name' => 'Employment Contract', 'description' => 'Employee terms, compensation, and responsibilities', 'previewImage' => 'contract', 'tags' => ['Legal', 'HR']],
                ['id' => 'contract-freelance', 'name' => 'Freelance Agreement', 'description' => 'Independent contractor project-based engagement', 'previewImage' => 'contract', 'tags' => ['Freelance', 'Project']],
            ],
            self::CV_RESUME => [
                ['id' => 'cv-classic', 'name' => 'Classic Professional', 'description' => 'Clean, text-focused layout optimized for ATS', 'previewImage' => 'cv', 'tags' => ['Professional', 'ATS']],
                ['id' => 'cv-modern', 'name' => 'Modern Creative', 'description' => 'Two-column layout with skills bars and accents', 'previewImage' => 'cv', 'tags' => ['Creative', 'Visual']],
                ['id' => 'cv-technical', 'name' => 'Technical Expert', 'description' => 'Focused on stack, skills, and project history', 'previewImage' => 'cv', 'tags' => ['Developer', 'Technical']],
                ['id' => 'cv-international', 'name' => 'International Standard', 'description' => 'Healthcare/MLS format with facility details, equipment, samples, and certifications', 'previewImage' => 'cv', 'tags' => ['Healthcare', 'International']],
            ],
            self::COVER_LETTER => [
                ['id' => 'cl-standard', 'name' => 'Standard Professional', 'description' => 'Traditional formal letter format', 'previewImage' => 'proposal', 'tags' => ['Formal', 'Traditional']],
                ['id' => 'cl-creative', 'name' => 'Modern Creative', 'description' => 'Contemporary layout with personal branding', 'previewImage' => 'proposal', 'tags' => ['Creative', 'Visual']],
            ],
            self::CUSTOM => [
                ['id' => 'custom-flexible', 'name' => 'Flexible Layout', 'description' => 'Customizable template that adapts to content', 'previewImage' => 'custom', 'tags' => ['Flexible', 'Custom']],
                ['id' => 'custom-case', 'name' => 'Case Study', 'description' => 'Client success story format', 'previewImage' => 'custom', 'tags' => ['Case Study', 'Results']],
            ],
        };
    }
}
