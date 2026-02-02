<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Grid Infrastructure Limits
    |--------------------------------------------------------------------------
    |
    | Defines the operational boundaries for each subscription tier.
    | These values are referenced throughout the application for:
    | - Sub-account quotas
    | - Monthly token allocations
    | - Feature availability
    |
    */
    'tiers' => [
        'starter' => [
            'name' => 'Starter Node',
            'max_sub_accounts' => 0,
            'monthly_tokens' => 5000,
            'features' => ['Basic Content Generation', 'Social Scheduling'],
        ],
        'pro' => [
            'name' => 'Pro Node',
            'max_sub_accounts' => 0,
            'monthly_tokens' => 25000,
            'features' => ['AI Agents', 'Knowledge Base', 'Brand Kits', 'Unlimited Generation'],
        ],
        'agency' => [
            'name' => 'Agency Node',
            'max_sub_accounts' => 10,
            'monthly_tokens' => 100000,
            'features' => ['All Pro Features', 'Sub-Account Management', 'White-labeling', 'API Access'],
        ],
    ],
];

