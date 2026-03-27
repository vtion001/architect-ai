<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Subscription plan types.
 *
 * STARTER: Default for new users - limited feature credits, no access to Pro features
 * PRO: Unlimited credits, access to AI Agents, Knowledge Base, Brand Kits
 * AGENCY: All Pro features + ability to create sub-accounts
 */
enum PlanType: string
{
    case STARTER = 'starter';
    case PRO = 'pro';
    case AGENCY = 'agency';

    /**
     * Get a human-readable label for the plan.
     */
    public function label(): string
    {
        return match ($this) {
            self::STARTER => 'Starter',
            self::PRO => 'Pro',
            self::AGENCY => 'Agency',
        };
    }

    /**
     * Check if this plan has unlimited feature credits.
     */
    public function hasUnlimitedCredits(): bool
    {
        return in_array($this, [self::PRO, self::AGENCY]);
    }

    /**
     * Check if this plan can create sub-accounts.
     */
    public function canCreateSubAccounts(): bool
    {
        return $this === self::AGENCY;
    }

    /**
     * Check if this plan has access to Pro features (AI Agents, KB, Brands).
     */
    public function hasProFeatures(): bool
    {
        return in_array($this, [self::PRO, self::AGENCY]);
    }
}
