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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
        'site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    ],

    'eskiz' => [
        'base_url' => env('ESKIZ_BASE_URL', 'https://notify.eskiz.uz'),
        'email' => env('ESKIZ_EMAIL'),
        'password' => env('ESKIZ_PASSWORD'),
        'from' => env('ESKIZ_FROM', '4546'),
        'admin_phone' => env('ADMIN_SMS_PHONE', '+998942551397'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'group_id' => env('TELEGRAM_GROUP_ID'),
    ],

    'meta' => [
        'page_access_token' => env('META_PAGE_ACCESS_TOKEN'),
        'facebook_page_id' => env('FACEBOOK_PAGE_ID'),
        'instagram_account_id' => env('INSTAGRAM_ACCOUNT_ID'),
    ],

    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID', env('GOOGLE_CLIENT_ID')),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET')),
        'refresh_token' => env('GOOGLE_YOUTUBE_REFRESH_TOKEN'),
    ],

    'social_links' => [
        'site' => env('APP_URL', 'https://motrix.uz'),
        'telegram' => env('SOCIAL_TELEGRAM_URL', 'https://t.me/Motrixuz'),
        'instagram' => env('SOCIAL_INSTAGRAM_URL', 'https://instagram.com/motrixuz'),
        'youtube' => env('SOCIAL_YOUTUBE_URL', 'https://youtube.com/@Motrixuz'),
        'facebook' => env('SOCIAL_FACEBOOK_URL', 'https://facebook.com/motrixuz'),
    ],

];
