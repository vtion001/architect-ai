<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Feature types for credit-based and access-gated features.
 *
 * CREDIT-BASED: Features that consume credits per use
 * - post_generator, video_generator, blog_generator, click_calendar, document_builder
 *
 * ACCESS-GATED: Features locked behind plan tiers (Pro+, Agency)
 * - ai_agents, knowledge_base, brand_kits, sub_accounts
 */
enum FeatureType: string
{
    // Credit-based features (Starter has limited uses)
    case POST_GENERATOR = 'post_generator';
    case VIDEO_GENERATOR = 'video_generator';
    case BLOG_GENERATOR = 'blog_generator';
    case CLICK_CALENDAR = 'click_calendar';
    case DOCUMENT_BUILDER = 'document_builder';

    // Access-gated features (Pro+ only)
    case AI_AGENTS = 'ai_agents';
    case KNOWLEDGE_BASE = 'knowledge_base';
    case BRAND_KITS = 'brand_kits';

    // Agency-only features
    case SUB_ACCOUNTS = 'sub_accounts';

    /**
     * Check if this feature is credit-based (has usage limits).
     */
    public function isCreditBased(): bool
    {
        return in_array($this, [
            self::POST_GENERATOR,
            self::VIDEO_GENERATOR,
            self::BLOG_GENERATOR,
            self::CLICK_CALENDAR,
            self::DOCUMENT_BUILDER,
        ]);
    }

    /**
     * Check if this feature is access-gated (plan-locked).
     */
    public function isAccessGated(): bool
    {
        return in_array($this, [
            self::AI_AGENTS,
            self::KNOWLEDGE_BASE,
            self::BRAND_KITS,
            self::SUB_ACCOUNTS,
        ]);
    }

    /**
     * Get a human-readable label for the feature.
     */
    public function label(): string
    {
        return match ($this) {
            self::POST_GENERATOR => 'Post Generator',
            self::VIDEO_GENERATOR => 'Video Generator',
            self::BLOG_GENERATOR => 'Blog Generator',
            self::CLICK_CALENDAR => '1-Click Calendar',
            self::DOCUMENT_BUILDER => 'Document Builder',
            self::AI_AGENTS => 'AI Agents',
            self::KNOWLEDGE_BASE => 'Knowledge Base',
            self::BRAND_KITS => 'Brand Kits',
            self::SUB_ACCOUNTS => 'Sub-Accounts',
        };
    }
}
