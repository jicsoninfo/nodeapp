<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Language;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // ── Languages ────────────────────────────────────────────────────────
        $en = Language::create(['code' => 'en', 'name' => 'English',  'native_name' => 'English',   'is_default' => true]);
        $ar = Language::create(['code' => 'ar', 'name' => 'Arabic',   'native_name' => 'العربية',   'is_rtl'     => true]);
        $fr = Language::create(['code' => 'fr', 'name' => 'French',   'native_name' => 'Français']);
        $de = Language::create(['code' => 'de', 'name' => 'German',   'native_name' => 'Deutsch']);

        // ── Currencies ───────────────────────────────────────────────────────
        $usd = Currency::create(['code' => 'USD', 'name' => 'US Dollar',     'symbol' => '$',  'symbol_position' => 'before', 'exchange_rate' => 1.0,    'is_default' => true]);
        $eur = Currency::create(['code' => 'EUR', 'name' => 'Euro',           'symbol' => '€',  'symbol_position' => 'after',  'exchange_rate' => 0.92]);
        $sar = Currency::create(['code' => 'SAR', 'name' => 'Saudi Riyal',    'symbol' => '﷼', 'symbol_position' => 'after',  'exchange_rate' => 3.75]);
        $gbp = Currency::create(['code' => 'GBP', 'name' => 'British Pound',  'symbol' => '£',  'symbol_position' => 'before', 'exchange_rate' => 0.79]);

        // ── Price Lists ───────────────────────────────────────────────────────
        $retail    = PriceList::create(['name' => 'Retail',    'code' => 'retail',    'is_default' => true]);
        $wholesale = PriceList::create(['name' => 'Wholesale', 'code' => 'wholesale']);

        // ── Categories ────────────────────────────────────────────────────────
        $electronics = Category::create(['slug' => 'electronics', 'sort_order' => 1]);
        $electronics->setTranslation('en', ['name' => 'Electronics', 'description' => 'Gadgets and devices']);
        $electronics->setTranslation('ar', ['name' => 'إلكترونيات',  'description' => 'الأجهزة والمعدات']);
        $electronics->setTranslation('fr', ['name' => 'Électronique','description' => 'Appareils et gadgets']);
        $electronics->setTranslation('de', ['name' => 'Elektronik',  'description' => 'Geräte und Gadgets']);

        $phones = Category::create(['slug' => 'phones', 'parent_id' => $electronics->id, 'sort_order' => 1]);
        $phones->setTranslation('en', ['name' => 'Phones']);
        $phones->setTranslation('ar', ['name' => 'هواتف']);
        $phones->setTranslation('fr', ['name' => 'Téléphones']);
        // Note: German intentionally omitted → will fall back to English

        $clothing = Category::create(['slug' => 'clothing', 'sort_order' => 2]);
        $clothing->setTranslation('en', ['name' => 'Clothing', 'description' => 'Apparel and accessories']);
        $clothing->setTranslation('ar', ['name' => 'ملابس',    'description' => 'الملابس والاكسسوارات']);
        $clothing->setTranslation('fr', ['name' => 'Vêtements','description' => 'Vêtements et accessoires']);
        $clothing->setTranslation('de', ['name' => 'Kleidung', 'description' => 'Bekleidung und Zubehör']);

        // ── Products ──────────────────────────────────────────────────────────
        $phone = Product::create([
            'sku' => 'PHN-001', 'slug' => 'smart-phone-x1',
            'category_id' => $phones->id, 'stock_quantity' => 50,
        ]);
        $phone->setTranslation('en', [
            'name'              => 'SmartPhone X1',
            'short_description' => 'Flagship smartphone with 200MP camera',
            'description'       => 'The SmartPhone X1 features a 6.7" AMOLED display, 200MP triple camera system, and 5000mAh battery.',
        ]);
        $phone->setTranslation('ar', [
            'name'              => 'سمارت فون X1',
            'short_description' => 'هاتف ذكي رائد مع كاميرا 200 ميغابكسل',
            'description'       => 'يتميز سمارت فون X1 بشاشة AMOLED مقاس 6.7 بوصة ونظام كاميرا ثلاثي.',
        ]);
        $phone->setTranslation('fr', [
            'name'              => 'SmartPhone X1',
            'short_description' => 'Smartphone phare avec caméra 200MP',
            'description'       => 'Le SmartPhone X1 dispose d\'un écran AMOLED de 6,7" et un système triple caméra 200MP.',
        ]);
        // German omitted — falls back to English

        // Prices for phone
        foreach ([$retail, $wholesale] as $list) {
            $multiplier = $list->code === 'wholesale' ? 0.8 : 1.0;
            ProductPrice::create(['product_id' => $phone->id, 'price_list_id' => $list->id, 'currency_code' => 'USD', 'amount' => 999  * $multiplier, 'sale_amount' => 899 * $multiplier]);
            ProductPrice::create(['product_id' => $phone->id, 'price_list_id' => $list->id, 'currency_code' => 'EUR', 'amount' => 919  * $multiplier]);
            ProductPrice::create(['product_id' => $phone->id, 'price_list_id' => $list->id, 'currency_code' => 'SAR', 'amount' => 3750 * $multiplier]);
            ProductPrice::create(['product_id' => $phone->id, 'price_list_id' => $list->id, 'currency_code' => 'GBP', 'amount' => 789  * $multiplier]);
        }

        $jacket = Product::create([
            'sku' => 'CLT-001', 'slug' => 'winter-jacket-pro',
            'category_id' => $clothing->id, 'stock_quantity' => 120,
        ]);
        $jacket->setTranslation('en', ['name' => 'Winter Jacket Pro', 'short_description' => 'Warm all-weather jacket']);
        $jacket->setTranslation('ar', ['name' => 'جاكيت الشتاء برو', 'short_description' => 'معطف دافئ لجميع الأحوال الجوية']);
        $jacket->setTranslation('fr', ['name' => 'Veste Hiver Pro',   'short_description' => 'Veste chaude pour tous temps']);
        $jacket->setTranslation('de', ['name' => 'Winterjacke Pro',   'short_description' => 'Warme Allwetterjacke']);

        foreach ([$retail, $wholesale] as $list) {
            $multiplier = $list->code === 'wholesale' ? 0.75 : 1.0;
            ProductPrice::create(['product_id' => $jacket->id, 'price_list_id' => $list->id, 'currency_code' => 'USD', 'amount' => 149 * $multiplier]);
            ProductPrice::create(['product_id' => $jacket->id, 'price_list_id' => $list->id, 'currency_code' => 'EUR', 'amount' => 139 * $multiplier]);
            ProductPrice::create(['product_id' => $jacket->id, 'price_list_id' => $list->id, 'currency_code' => 'SAR', 'amount' => 560 * $multiplier]);
            ProductPrice::create(['product_id' => $jacket->id, 'price_list_id' => $list->id, 'currency_code' => 'GBP', 'amount' => 119 * $multiplier]);
        }
    }
}
