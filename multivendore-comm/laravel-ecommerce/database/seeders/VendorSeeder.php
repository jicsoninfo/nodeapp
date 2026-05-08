<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProfile;
use App\Models\VendorPolicy;
use App\Models\VendorTranslation;
use App\Models\VendorBankAccount;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendorDefs = [
            [
                'owner_email'    => 'rahul.sharma@techstore.in',
                'store_name'     => 'TechStore India',
                'slug'           => 'techstore-india',
                'status'         => 'active',
                'plan_type'      => 'pro',
                'commission_rate'=> 12.50,
                'profile' => [
                    'description'   => "India's #1 destination for smartphones, laptops, and accessories.",
                    'business_type' => 'company',
                    'tax_id'        => 'GSTIN29AAFCS5699Q1Z9',
                    'avg_rating'    => 4.72,
                    'total_reviews' => 38420,
                    'website_url'   => 'https://techstore.in',
                ],
                'translations' => [
                    ['lang_code' => 'en', 'description' => "India's #1 destination for smartphones, laptops, and accessories."],
                    ['lang_code' => 'hi', 'description' => 'भारत का नंबर 1 स्मार्टफोन, लैपटॉप और एक्सेसरीज़ का गंतव्य।'],
                ],
                'policies' => [
                    ['type' => 'return',   'return_window_days' => 10,  'lang_code' => 'en', 'condition' => 'Item must be unused in original packaging.'],
                    ['type' => 'shipping', 'return_window_days' => null, 'lang_code' => 'en', 'condition' => 'Free shipping on orders above ₹999.'],
                    ['type' => 'warranty', 'return_window_days' => null, 'lang_code' => 'en', 'condition' => '6-month extended warranty on all electronics.'],
                ],
                'bank' => ['account_holder' => 'TechStore India Pvt Ltd', 'bank_name' => 'HDFC Bank', 'account_number_enc' => 'ENC:acc_TS_HDFC_001', 'routing_number_enc' => 'ENC:ifsc_HDFC0001234'],
            ],
            [
                'owner_email'    => 'emma.johnson@fashionhub.com',
                'store_name'     => 'FashionHub Global',
                'slug'           => 'fashionhub-global',
                'status'         => 'active',
                'plan_type'      => 'enterprise',
                'commission_rate'=> 10.00,
                'profile' => [
                    'description'   => 'Global fashion brand offering premium clothing, footwear, and accessories.',
                    'business_type' => 'brand',
                    'tax_id'        => 'EIN-45-2341789',
                    'avg_rating'    => 4.55,
                    'total_reviews' => 72100,
                    'website_url'   => 'https://fashionhub.com',
                ],
                'translations' => [
                    ['lang_code' => 'en', 'description' => 'Global fashion brand offering premium clothing and accessories.'],
                    ['lang_code' => 'de', 'description' => 'Globale Modemarke mit Premium-Kleidung und Accessoires.'],
                    ['lang_code' => 'fr', 'description' => 'Marque de mode mondiale proposant vêtements et accessoires premium.'],
                    ['lang_code' => 'ar', 'description' => 'علامة أزياء عالمية تقدم ملابس وإكسسوارات فاخرة.'],
                ],
                'policies' => [
                    ['type' => 'return',   'return_window_days' => 30, 'lang_code' => 'en', 'condition' => 'Free returns within 30 days.'],
                    ['type' => 'shipping', 'return_window_days' => null,'lang_code' => 'en', 'condition' => 'Free standard shipping worldwide on orders above $50.'],
                ],
                'bank' => ['account_holder' => 'FashionHub LLC', 'bank_name' => 'Chase Bank', 'account_number_enc' => 'ENC:acc_FH_CHASE_001', 'routing_number_enc' => 'ENC:rtn_021000021'],
            ],
            [
                'owner_email'    => 'ahmed.ali@bookworld.ae',
                'store_name'     => 'BookWorld ME',
                'slug'           => 'bookworld-me',
                'status'         => 'active',
                'plan_type'      => 'basic',
                'commission_rate'=> 15.00,
                'profile' => [
                    'description'   => 'Curated multilingual books across fiction, non-fiction, and academic categories.',
                    'business_type' => 'company',
                    'tax_id'        => 'TRN100234567890003',
                    'avg_rating'    => 4.60,
                    'total_reviews' => 8930,
                    'website_url'   => 'https://bookworld.ae',
                ],
                'translations' => [
                    ['lang_code' => 'en', 'description' => 'Curated multilingual book collection across all genres.'],
                    ['lang_code' => 'ar', 'description' => 'مجموعة كتب متعددة اللغات منتقاة بعناية.'],
                ],
                'policies' => [
                    ['type' => 'return', 'return_window_days' => 14, 'lang_code' => 'en', 'condition' => 'Books returnable within 14 days if defective.'],
                    ['type' => 'return', 'return_window_days' => 14, 'lang_code' => 'ar', 'condition' => 'يمكن إرجاع الكتب خلال 14 يومًا إذا كانت معيبة.'],
                ],
                'bank' => ['account_holder' => 'BookWorld Trading LLC', 'bank_name' => 'Emirates NBD', 'account_number_enc' => 'ENC:acc_BW_ENBD_001', 'routing_number_enc' => null],
            ],
            [
                'owner_email'    => 'li.wei@gadgetzone.cn',
                'store_name'     => 'GadgetZone CN',
                'slug'           => 'gadgetzone-cn',
                'status'         => 'active',
                'plan_type'      => 'pro',
                'commission_rate'=> 13.00,
                'profile' => [
                    'description'   => 'Cutting-edge gadgets sourced directly from Shenzhen manufacturers.',
                    'business_type' => 'company',
                    'tax_id'        => 'CN91110108MA01234567',
                    'avg_rating'    => 4.30,
                    'total_reviews' => 15600,
                    'website_url'   => null,
                ],
                'translations' => [
                    ['lang_code' => 'en', 'description' => 'Cutting-edge gadgets sourced directly from Shenzhen.'],
                    ['lang_code' => 'zh', 'description' => '直接来自深圳的尖端电子产品，价格最优惠。'],
                ],
                'policies' => [
                    ['type' => 'return',   'return_window_days' => 7,   'lang_code' => 'en', 'condition' => '7-day return in original sealed packaging.'],
                    ['type' => 'warranty', 'return_window_days' => null, 'lang_code' => 'en', 'condition' => '12 months manufacturer warranty.'],
                ],
                'bank' => ['account_holder' => 'GadgetZone Tech Co', 'bank_name' => 'Bank of China', 'account_number_enc' => 'ENC:acc_GZ_BOC_001', 'routing_number_enc' => null],
            ],
        ];

        foreach ($vendorDefs as $def) {
            $owner  = User::where('email', $def['owner_email'])->first();

            $vendor = Vendor::create([
                'owner_user_id'  => $owner->id,
                'store_name'     => $def['store_name'],
                'slug'           => $def['slug'],
                'status'         => $def['status'],
                'plan_type'      => $def['plan_type'],
                'commission_rate'=> $def['commission_rate'],
                'approved_at'    => now(),
            ]);

            VendorProfile::create(array_merge(['vendor_id' => $vendor->id], $def['profile']));

            foreach ($def['translations'] as $t) {
                VendorTranslation::create(array_merge(['vendor_id' => $vendor->id], $t));
            }

            foreach ($def['policies'] as $p) {
                VendorPolicy::create(array_merge(['vendor_id' => $vendor->id], $p));
            }

            VendorBankAccount::create(array_merge(
                ['vendor_id' => $vendor->id, 'is_primary' => true],
                $def['bank']
            ));
        }

        $this->command->info('  Vendors seeded (4)');
    }
}
