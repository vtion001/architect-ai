<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Developer Access
    |--------------------------------------------------------------------------
    |
    | This email is granted "Developer" status, allowing observability across 
    | all tenants. Actions performed by this user are strictly audited.
    |
    */
    'developer_email' => env('IAM_DEVELOPER_EMAIL', 'admin@architect-ai.io'),

    /*
    |--------------------------------------------------------------------------
    | Session Defaults
    |--------------------------------------------------------------------------
    */
    'sessions' => [
        'developer' => [
            'max_duration' => 240, // 4 hours
            'inactivity_timeout' => 30, // 30 mins
        ],
        'agency_owner' => [
            'max_duration' => 480, // 8 hours
            'inactivity_timeout' => 15,
        ],
        'agency_admin' => [
            'max_duration' => 480,
            'inactivity_timeout' => 30,
        ],
        'sub_account_user' => [
            'max_duration' => 720, // 12 hours
            'inactivity_timeout' => 60,
        ],
    ],
];
