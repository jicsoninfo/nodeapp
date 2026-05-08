<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Platform Settings
    |--------------------------------------------------------------------------
    */
    'name'                    => env('APP_NAME', 'Marketplace SaaS'),
    'currency'                => env('PLATFORM_CURRENCY', 'USD'),
    'timezone'                => env('PLATFORM_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Commission
    |--------------------------------------------------------------------------
    */
    'commission_default'      => (float) env('PLATFORM_COMMISSION_DEFAULT', 15.00),
    'min_payout_amount'       => 50.00,   // USD — minimum balance before payout is triggered
    'payout_schedule'         => 'monthly', // monthly | weekly

    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */
    'cart' => [
        'auth_expiry_hours'  => 168,  // 7 days for logged-in users
        'guest_expiry_hours' => 24,   // 24 hrs for guests
        'max_items'          => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Orders & Tax
    |--------------------------------------------------------------------------
    */
    'tax_rate_default'        => 9.00,    // %
    'return_window_days'      => 30,

    /*
    |--------------------------------------------------------------------------
    | Shipping
    |--------------------------------------------------------------------------
    */
    'shipping' => [
        'free_threshold' => 50.00,  // USD — free shipping above this
        'default_cost'   => 5.99,   // USD
    ],

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */
    'search' => [
        'results_per_page' => 20,
        'max_per_page'     => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'wishlists'          => (bool) env('FEATURE_WISHLISTS', true),
        'multi_currency'     => (bool) env('FEATURE_MULTI_CURRENCY', true),
        'live_chat'          => (bool) env('FEATURE_LIVE_CHAT', false),
        'ai_recommendations' => (bool) env('FEATURE_AI_RECOMMENDATIONS', false),
        'vendor_analytics'   => (bool) env('FEATURE_VENDOR_ANALYTICS', true),
        'guest_checkout'     => true,
        'auto_approve_vendor'=> false,
        'auto_approve_review'=> false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    */
    'locales' => ['en', 'hi', 'ar', 'de', 'fr', 'zh', 'ja', 'es', 'pt', 'ru'],

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies (display)
    |--------------------------------------------------------------------------
    */
    'currencies' => ['USD', 'EUR', 'INR', 'GBP', 'JPY', 'AED', 'CNY', 'SAR', 'BRL', 'CAD'],
];
