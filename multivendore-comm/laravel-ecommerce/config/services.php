<?php
return [
    'postmark' => ['token' => env('POSTMARK_TOKEN')],
    'ses'      => ['key' => env('AWS_ACCESS_KEY_ID'), 'secret' => env('AWS_SECRET_ACCESS_KEY'), 'region' => env('AWS_DEFAULT_REGION', 'us-east-1')],
    'resend'   => ['key' => env('RESEND_KEY')],
    'slack'    => ['notifications' => ['bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'), 'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL')]],

    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'platform_account' => env('STRIPE_PLATFORM_ACCOUNT'),
    ],

    'razorpay' => [
        'key'            => env('RAZORPAY_KEY'),
        'secret'         => env('RAZORPAY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'server_key' => env('FIREBASE_SERVER_KEY'),
    ],

    'twilio' => [
        'sid'       => env('TWILIO_SID'),
        'token'     => env('TWILIO_AUTH_TOKEN'),
        'from'      => env('TWILIO_FROM'),
    ],

    'exchange_rates' => [
        'api_key' => env('EXCHANGE_RATES_API_KEY'),
    ],
];
