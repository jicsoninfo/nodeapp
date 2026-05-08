<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $v1 = Vendor::where('slug', 'techstore-india')->first();
        $v2 = Vendor::where('slug', 'fashionhub-global')->first();
        $v3 = Vendor::where('slug', 'bookworld-me')->first();

        $coupons = [
            ['code' => 'WELCOME10',  'type' => 'percent',      'value' => 10,  'min_order' => 500,   'usage_limit' => 1000, 'vendor_id' => null,        'expires_at' => '2026-12-31'],
            ['code' => 'FLAT500',    'type' => 'fixed',        'value' => 500, 'min_order' => 2000,  'usage_limit' => 500,  'vendor_id' => null,        'expires_at' => '2026-06-30'],
            ['code' => 'FREESHIP',   'type' => 'free_shipping','value' => 0,   'min_order' => 300,   'usage_limit' => null, 'vendor_id' => null,        'expires_at' => '2026-12-31'],
            ['code' => 'FASHION20',  'type' => 'percent',      'value' => 20,  'min_order' => 50,    'usage_limit' => 300,  'vendor_id' => $v2?->id,    'expires_at' => '2026-09-30'],
            ['code' => 'TECH15OFF',  'type' => 'percent',      'value' => 15,  'min_order' => 5000,  'usage_limit' => 200,  'vendor_id' => $v1?->id,    'expires_at' => '2026-08-31'],
            ['code' => 'BOOK50',     'type' => 'fixed',        'value' => 50,  'min_order' => 200,   'usage_limit' => 1000, 'vendor_id' => $v3?->id,    'expires_at' => '2026-12-31'],
            ['code' => 'DIWALI25',   'type' => 'percent',      'value' => 25,  'min_order' => 1000,  'usage_limit' => 500,  'vendor_id' => null,        'expires_at' => '2026-11-15'],
            ['code' => 'NEWUSER',    'type' => 'fixed',        'value' => 200, 'min_order' => 0,     'usage_limit' => null, 'vendor_id' => null,        'expires_at' => '2026-12-31'],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }

        $this->command->info('  Coupons seeded (8)');
    }
}
