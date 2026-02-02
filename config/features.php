<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans Configuration
    |--------------------------------------------------------------------------
    |
    | Defines feature access and credit limits for each subscription tier.
    |
    | CREDIT VALUES:
    | - Positive number: Limited uses per month
    | - -1: Unlimited uses
    | - 0: Feature disabled
    |
    | ACCESS VALUES:
    | - true: Feature is accessible
    | - false: Feature is locked (requires upgrade)
    |
    */

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'description' => 'Perfect for getting started with AI-powered content creation.',
            
            // Credit-based features (monthly limits)
            'credits' => [
                'post_generator' => 3,
                'video_generator' => 1,
                'blog_generator' => 1,
                'click_calendar' => 0,
                'document_builder' => 1,
            ],
            
            // Access-gated features (plan-locked)
            'access' => [
                'ai_agents' => false,
                'knowledge_base' => false,
                'brand_kits' => false,
                'sub_accounts' => false,
            ],
            
            // Token allocation (for AI processing)
            'monthly_tokens' => 5000,
        ],

        'pro' => [
            'name' => 'Pro',
            'description' => 'Full access to all AI features with unlimited content generation.',
            
            // Credit-based features (all unlimited)
            'credits' => [
                'post_generator' => -1, // Unlimited
                'video_generator' => -1,
                'blog_generator' => -1,
                'click_calendar' => -1,
                'document_builder' => -1,
            ],
            
            // Access-gated features (Pro unlocks AI features)
            'access' => [
                'ai_agents' => true,
                'knowledge_base' => true,
                'brand_kits' => true,
                'sub_accounts' => false, // Still locked - Agency only
            ],
            
            // Token allocation
            'monthly_tokens' => 25000,
        ],

        'agency' => [
            'name' => 'Agency',
            'description' => 'Enterprise-grade features with client management and sub-accounts.',
            
            // Credit-based features (all unlimited)
            'credits' => [
                'post_generator' => -1,
                'video_generator' => -1,
                'blog_generator' => -1,
                'click_calendar' => -1,
                'document_builder' => -1,
            ],
            
            // Access-gated features (All features unlocked)
            'access' => [
                'ai_agents' => true,
                'knowledge_base' => true,
                'brand_kits' => true,
                'sub_accounts' => true,
            ],
            
            // Token allocation
            'monthly_tokens' => 100000,
            
            // Agency-specific limits
            'max_sub_accounts' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit Reset Configuration
    |--------------------------------------------------------------------------
    */

    'reset' => [
        // When to reset credits (start of each month)
        'schedule' => 'monthly',
        
        // Whether to carry over unused credits
        'carry_over' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Developer Bypass
    |--------------------------------------------------------------------------
    |
    | Developer accounts bypass all feature restrictions.
    | This is determined by the IAM config developer_email setting.
    |
    */

    'developer_bypass' => true,
];
