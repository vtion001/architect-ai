<?php

return [
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
    ],

    'minimax' => [
        'key' => env('MINIMAX_API_KEY'),
        'model' => env('MINIMAX_MODEL', 'minimax-m2.7'),
        'base_url' => env('MINIMAX_BASE_URL', 'https://api.minimax.io/v1'),
    ],
    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),
        'resume_model' => env('OPENROUTER_RESUME_MODEL', 'arcee/arcee-trinity-large-preview'),
        'chat_model' => env('OPENROUTER_CHAT_MODEL', 'cognitivecomputations/gpt-oss-120b'),
        'content_model' => env('OPENROUTER_CONTENT_MODEL', 'zhipu-ai/glm-4.5-air'),
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1/chat/completions'),
    ],
    'perplexity' => [
        'key' => env('PERPLEXITY_API_KEY'),
    ],
    'hiker_api' => [
        'key' => env('HIKER_API_KEY'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_REDIRECT_URI'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'instagram' => [
        'client_id' => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
        'redirect' => env('INSTAGRAM_REDIRECT_URI'),
    ],

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => trim(str_replace('\n', '', env('CLOUDINARY_API_SECRET', ''))),
    ],

    'hellosign' => [
        'api_key' => env('HELLOSIGN_API_KEY'),
        'client_id' => env('HELLOSIGN_CLIENT_ID'),
        'test_mode' => env('HELLOSIGN_TEST_MODE', true),
    ],
];
