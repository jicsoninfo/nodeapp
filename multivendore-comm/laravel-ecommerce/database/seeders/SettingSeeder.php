<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->truncate();

        $settings = [
            // General
            ['key' => 'site_name',                  'value' => 'Marketplace SaaS',  'type' => 'string',  'group' => 'general',   'description' => 'Platform display name'],
            ['key' => 'site_tagline',               'value' => 'Shop Everything',   'type' => 'string',  'group' => 'general',   'description' => 'Tagline shown on homepage'],
            ['key' => 'support_email',              'value' => 'support@marketplace.com', 'type' => 'string', 'group' => 'general', 'description' => 'Support contact email'],
            ['key' => 'support_phone',              'value' => '+1-800-MARKET',     'type' => 'string',  'group' => 'general',   'description' => 'Support phone number'],
            ['key' => 'default_currency',           'value' => 'USD',               'type' => 'string',  'group' => 'general',   'description' => 'Default platform currency'],
            ['key' => 'default_language',           'value' => 'en',                'type' => 'string',  'group' => 'general',   'description' => 'Default platform language'],
            ['key' => 'default_timezone',           'value' => 'UTC',               'type' => 'string',  'group' => 'general',   'description' => 'Default platform timezone'],
            ['key' => 'maintenance_mode',           'value' => '0',                 'type' => 'boolean', 'group' => 'general',   'description' => 'Toggle maintenance mode'],

            // Commerce
            ['key' => 'default_commission_rate',    'value' => '15.00',             'type' => 'string',  'group' => 'commerce',  'description' => 'Default vendor commission %'],
            ['key' => 'min_payout_amount',          'value' => '50.00',             'type' => 'string',  'group' => 'commerce',  'description' => 'Minimum payout amount in USD'],
            ['key' => 'payout_schedule',            'value' => 'monthly',           'type' => 'string',  'group' => 'commerce',  'description' => 'Payout schedule (weekly/monthly)'],
            ['key' => 'auto_approve_vendors',       'value' => '0',                 'type' => 'boolean', 'group' => 'commerce',  'description' => 'Auto-approve new vendor applications'],
            ['key' => 'max_cart_items',             'value' => '50',                'type' => 'integer', 'group' => 'commerce',  'description' => 'Maximum items allowed in cart'],
            ['key' => 'cart_expiry_hours',          'value' => '168',               'type' => 'integer', 'group' => 'commerce',  'description' => 'Cart expiry in hours (168 = 7 days)'],
            ['key' => 'guest_cart_expiry_hours',    'value' => '24',                'type' => 'integer', 'group' => 'commerce',  'description' => 'Guest cart expiry in hours'],
            ['key' => 'enable_guest_checkout',      'value' => '1',                 'type' => 'boolean', 'group' => 'commerce',  'description' => 'Allow checkout without login'],
            ['key' => 'tax_rate_default',           'value' => '9.00',              'type' => 'string',  'group' => 'commerce',  'description' => 'Default tax rate percentage'],

            // Shipping
            ['key' => 'free_shipping_threshold',    'value' => '50.00',             'type' => 'string',  'group' => 'shipping',  'description' => 'Order amount for free shipping (USD)'],
            ['key' => 'default_shipping_cost',      'value' => '5.99',              'type' => 'string',  'group' => 'shipping',  'description' => 'Default shipping cost (USD)'],

            // Reviews
            ['key' => 'auto_approve_reviews',       'value' => '0',                 'type' => 'boolean', 'group' => 'reviews',   'description' => 'Auto-approve submitted reviews'],
            ['key' => 'require_purchase_for_review','value' => '1',                 'type' => 'boolean', 'group' => 'reviews',   'description' => 'Require verified purchase to review'],
            ['key' => 'max_review_media',           'value' => '5',                 'type' => 'integer', 'group' => 'reviews',   'description' => 'Max images/videos per review'],

            // Features
            ['key' => 'feature_wishlists',          'value' => '1',                 'type' => 'boolean', 'group' => 'features',  'description' => 'Enable wishlists'],
            ['key' => 'feature_multi_currency',     'value' => '1',                 'type' => 'boolean', 'group' => 'features',  'description' => 'Enable multi-currency display'],
            ['key' => 'feature_live_chat',          'value' => '0',                 'type' => 'boolean', 'group' => 'features',  'description' => 'Enable live chat widget'],
            ['key' => 'feature_ai_recommendations', 'value' => '0',                 'type' => 'boolean', 'group' => 'features',  'description' => 'Enable AI product recommendations'],
            ['key' => 'feature_vendor_analytics',   'value' => '1',                 'type' => 'boolean', 'group' => 'features',  'description' => 'Enable vendor analytics dashboard'],

            // SEO
            ['key' => 'meta_title',                 'value' => 'Marketplace — Shop Everything', 'type' => 'string', 'group' => 'seo', 'description' => 'Default meta title'],
            ['key' => 'meta_description',           'value' => 'Find the best products from thousands of verified vendors.', 'type' => 'string', 'group' => 'seo', 'description' => 'Default meta description'],

            // Payment
            ['key' => 'payment_gateway_primary',    'value' => 'stripe',            'type' => 'string',  'group' => 'payment',   'description' => 'Primary payment gateway'],
            ['key' => 'payment_cod_enabled',        'value' => '1',                 'type' => 'boolean', 'group' => 'payment',   'description' => 'Enable cash on delivery'],
            ['key' => 'payment_cod_max_amount',     'value' => '200.00',            'type' => 'string',  'group' => 'payment',   'description' => 'Max order value for COD (USD)'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('  Settings seeded (' . count($settings) . ' entries)');
    }
}
