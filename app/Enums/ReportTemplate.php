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
            self::CUSTOM => '#7c3aed',
        };
    }

    /**
     * @return array<int, array{id: string, name: string, description: string, previewImage: string, tags: string[]}>
     */
    public function variants(): array
    {
        return match ($this) {
            self::EXECUTIVE_SUMMARY => [
                ['id' => 'exec-corporate', 'name' => 'Corporate Profile', 'description' => 'Professional company overview with key metrics and milestones', 'previewImage' => 'corporate', 'tags' => ['Professional', 'Metrics']],
                ['id' => 'exec-minimal', 'name' => 'Minimal Executive', 'description' => 'Clean, minimalist layout focused on key insights', 'previewImage' => 'minimal', 'tags' => ['Minimal', 'Clean']],
                ['id' => 'exec-detailed', 'name' => 'Comprehensive Report', 'description' => 'Detailed analysis with sections for findings and actions', 'previewImage' => 'detailed', 'tags' => ['Detailed', 'Charts']],
            ],
            self::MARKET_ANALYSIS => [
                ['id' => 'market-overview', 'name' => 'Market Overview', 'description' => 'Comprehensive market landscape with trends and threats', 'previewImage' => 'market', 'tags' => ['Trends', 'SWOT']],
                ['id' => 'market-competitive', 'name' => 'Competitive Landscape', 'description' => 'Focus on competitor benchmarks', 'previewImage' => 'competitive', 'tags' => ['Competitors', 'Benchmarks']],
                ['id' => 'market-segment', 'name' => 'Segment Analysis', 'description' => 'Deep dive into market segments', 'previewImage' => 'segment', 'tags' => ['Segments', 'Demographics']],
            ],
            self::FINANCIAL_OVERVIEW => [
                ['id' => 'fin-dashboard', 'name' => 'Financial Dashboard', 'description' => 'Key financial metrics with performance indicators', 'previewImage' => 'dashboard', 'tags' => ['Metrics', 'Ratios']],
                ['id' => 'fin-forecast', 'name' => 'Forecast Report', 'description' => 'Financial projections and scenarios', 'previewImage' => 'forecast', 'tags' => ['Projections', 'Scenarios']],
                ['id' => 'fin-quarterly', 'name' => 'Quarterly Review', 'description' => 'Period-over-period comparison', 'previewImage' => 'quarterly', 'tags' => ['Quarterly', 'Variance']],
            ],
            self::COMPETITIVE_INTELLIGENCE => [
                ['id' => 'intel-battlecard', 'name' => 'Competitor Battlecard', 'description' => 'Quick-reference competitive intelligence', 'previewImage' => 'battlecard', 'tags' => ['Quick Ref', 'SWOT']],
                ['id' => 'intel-deep', 'name' => 'Deep Dive Analysis', 'description' => 'Comprehensive competitor profiling', 'previewImage' => 'deepdive', 'tags' => ['Strategy', 'Detailed']],
            ],
            self::INFOGRAPHIC => [
                ['id' => 'infographic-corporate', 'name' => 'Corporate Profile', 'description' => 'Professional company overview with key metrics and milestones', 'previewImage' => 'corporate', 'tags' => ['Professional', 'Metrics']],
                ['id' => 'infographic-startup', 'name' => 'Startup Pitch', 'description' => 'Visual pitch deck style with problem/solution fit', 'previewImage' => 'startup', 'tags' => ['Pitch', 'Visual']],
            ],
            self::TREND_ANALYSIS => [
                ['id' => 'trend-timeline', 'name' => 'Trend Timeline', 'description' => 'Historical analysis with future projections', 'previewImage' => 'timeline', 'tags' => ['Historical', 'Timeline']],
                ['id' => 'trend-industry', 'name' => 'Industry Trends', 'description' => 'Sector-specific trends with implications', 'previewImage' => 'industry', 'tags' => ['Industry', 'Strategic']],
            ],
            self::CUSTOM => [
                ['id' => 'custom-flexible', 'name' => 'Flexible Layout', 'description' => 'Customizable template that adapts to content', 'previewImage' => 'flexible', 'tags' => ['Flexible', 'Custom']],
                ['id' => 'custom-case', 'name' => 'Case Study', 'description' => 'Client success story format', 'previewImage' => 'casestudy', 'tags' => ['Case Study', 'Results']],
            ],
        };
    }
}
