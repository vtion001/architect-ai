<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Token Costs
    |--------------------------------------------------------------------------
    |
    | Define the token cost for each operation. These values are used by
    | TokenService to consume tokens for various AI-powered features.
    |
    */

    'costs' => [
        // Content Generation
        'content_generation' => env('TOKEN_COST_CONTENT', 10),
        'content_batch' => env('TOKEN_COST_CONTENT_BATCH', 25),
        'social_post' => env('TOKEN_COST_SOCIAL_POST', 10),

        // Research
        'research' => env('TOKEN_COST_RESEARCH', 50),
        'research_deep' => env('TOKEN_COST_RESEARCH_DEEP', 100),

        // Documents
        'document_generation' => env('TOKEN_COST_DOCUMENT', 30),

        // AI Features
        'ai_chat_message' => env('TOKEN_COST_AI_CHAT', 5),
        'image_generation' => env('TOKEN_COST_IMAGE', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Initial Token Grant
    |--------------------------------------------------------------------------
    |
    | Number of tokens granted to new tenants upon registration.
    |
    */

    'initial_grant' => env('TOKEN_INITIAL_GRANT', 1000),

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    |
    | Default expiration period for token allocations (in days).
    | Set to null for no expiration.
    |
    */

    'expiration_days' => env('TOKEN_EXPIRATION_DAYS', null),
];
