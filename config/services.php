<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'exercise_runtime_enabled' => env('OPENAI_EXERCISE_RUNTIME_ENABLED', false),
        'http_referer' => env('OPENAI_HTTP_REFERER'),
        'app_title' => env('OPENAI_APP_TITLE', env('APP_NAME', 'Lexi')),
        'default_token_rates' => [
            'input_per_million' => 0.30,
            'output_per_million' => 1.20,
        ],
        'token_rates' => [
            'gpt-4o-mini' => [
                'input_per_million' => 0.15,
                'output_per_million' => 0.60,
            ],
        ],
    ],

];
