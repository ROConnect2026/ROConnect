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

    'turn' => [
        'username' => env('TURN_USERNAME'),
        'credential' => env('TURN_CREDENTIAL'),
    ],

    'translation' => [
        'provider' => env('TRANSLATION_PROVIDER', 'translateapi'),
        'base_url' => env('TRANSLATE_API_BASE_URL', 'https://api.translateapi.ai/api/v1/'),
        'api_key' => env('TRANSLATE_API_KEY'),
        'timeout' => (int) env('TRANSLATE_API_TIMEOUT', 8),
        'verify_ssl' => filter_var(env('TRANSLATE_API_VERIFY_SSL', true), FILTER_VALIDATE_BOOL),
        'ca_bundle' => env('TRANSLATE_API_CA_BUNDLE'),
        'language_cache_ttl_minutes' => (int) env('TRANSLATE_LANGUAGE_CACHE_TTL_MINUTES', 720),
        'cache_keys' => [
            'languages' => env('TRANSLATE_LANGUAGE_CACHE_KEY', 'translation.languages'),
            'stats_prefix' => env('TRANSLATE_STATS_CACHE_PREFIX', 'translation.stats'),
        ],
        'max_text_length' => (int) env('TRANSLATION_MAX_TEXT_LENGTH', 5000),
        'default_source_language' => env('TRANSLATION_DEFAULT_SOURCE_LANGUAGE', 'auto'),
    ],

];
