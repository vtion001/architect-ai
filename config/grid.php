<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Grid Infrastructure Limits
    |--------------------------------------------------------------------------
    |
    | Defines the operational boundaries for each tier.
    |
    */
    'tiers' => [
        'standard' => [
            'name' => 'Standard Node',
            'max_sub_accounts' => 3,
            'monthly_tokens' => 5000,
            'features' => ['Basic RAG', 'Social Scheduling'],
        ],
        'enterprise' => [
            'name' => 'Enterprise Node',
            'max_sub_accounts' => 15,
            'monthly_tokens' => 25000,
            'features' => ['Industrial RAG', 'White-labeling', 'API Access'],
        ],
        'master' => [
            'name' => 'Master Node',
            'max_sub_accounts' => 999, // Practically unlimited
            'monthly_tokens' => 1000000,
            'features' => ['Global RAG Sync', 'SSO Protocol', 'Dedicated Hardware'],
        ],
    ],
];
