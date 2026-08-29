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

    /*
    |--------------------------------------------------------------------------
    | Singida TTC (NMB control numbers + mirrored admissions)
    |--------------------------------------------------------------------------
    */
    'singida' => [
        'enabled' => filter_var(env('SINGIDA_INTEGRATION_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        // Local: http://localhost/singida/public  | Production: https://singidattc.ac.tz
        'base_url' => rtrim((string) env('SINGIDA_BASE_URL', 'https://singidattc.ac.tz'), '/'),
        'api_token' => env('SINGIDA_API_TOKEN'),
        'timeout' => (int) env('SINGIDA_API_TIMEOUT', 45),
        'default_amount' => (float) env('SINGIDA_APPLICATION_FEE', 20000),
        // Token Singida must send when calling SUPA payment callback
        'callback_token' => env('SINGIDA_CALLBACK_TOKEN', env('SINGIDA_API_TOKEN')),
        'callback_secret' => env('SINGIDA_CALLBACK_SECRET'),
        'allow_insecure_callback' => filter_var(env('SINGIDA_ALLOW_INSECURE_CALLBACK', false), FILTER_VALIDATE_BOOLEAN),
    ],

];
