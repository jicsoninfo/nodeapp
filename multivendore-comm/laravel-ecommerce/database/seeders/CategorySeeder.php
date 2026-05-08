<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            ['slug' => 'electronics', 'depth' => 0, 'sort' => 1,
             'translations' => [
                 'en' => ['Electronics',  'Gadgets, devices and all things tech'],
                 'hi' => ['इलेक्ट्रॉनिक्स', 'गैजेट, डिवाइस और टेक'],
                 'ar' => ['إلكترونيات',   'الأجهزة والتكنولوجيا'],
                 'de' => ['Elektronik',   'Gadgets, Geräte und Technik'],
                 'fr' => ['Électronique', 'Gadgets, appareils et technologie'],
                 'zh' => ['电子产品',      '数码设备和科技产品'],
                 'ja' => ['電子製品',      'ガジェット・デバイス・テック'],
             ],
             'children' => [
                 ['slug' => 'smartphones', 'depth' => 1, 'sort' => 1, 'translations' => ['en' => ['Smartphones', null], 'hi' => ['स्मार्टफ़ोन', null], 'ar' => ['الهواتف الذكية', null], 'de' => ['Smartphones', null], 'zh' => ['智能手机', null], 'ja' => ['スマートフォン', null]]],
                 ['slug' => 'laptops',     'depth' => 1, 'sort' => 2, 'translations' => ['en' => ['Laptops', null], 'hi' => ['लैपटॉप', null], 'ar' => ['أجهزة الكمبيوتر المحمول', null], 'de' => ['Laptops', null], 'zh' => ['笔记本电脑', null]]],
                 ['slug' => 'tablets',     'depth' => 1, 'sort' => 3, 'translations' => ['en' => ['Tablets', null], 'ar' => ['الأجهزة اللوحية', null]]],
                 ['slug' => 'audio',       'depth' => 1, 'sort' => 4, 'translations' => ['en' => ['Audio', null], 'de' => ['Audio', null], 'ar' => ['صوتيات', null]]],
                 ['slug' => 'cameras',     'depth' => 1, 'sort' => 5, 'translations' => ['en' => ['Cameras', null], 'ar' => ['الكاميرات', null]]],
                 ['slug' => 'smart-home',  'depth' => 1, 'sort' => 6, 'translations' => ['en' => ['Smart Home', null], 'ar' => ['المنزل الذكي', null]]],
             ],
            ],
            ['slug' => 'fashion', 'depth' => 0, 'sort' => 2,
             'translations' => [
                 'en' => ['Fashion', 'Clothing, footwear, and accessories'],
                 'hi' => ['फ़ैशन', 'कपड़े, जूते और एक्सेसरीज़'],
                 'ar' => ['أزياء', 'ملابس وأحذية وإكسسوارات'],
                 'de' => ['Mode', 'Kleidung, Schuhe und Accessoires'],
                 'fr' => ['Mode', 'Vêtements, chaussures et accessoires'],
                 'es' => ['Moda', 'Ropa, calzado y accesorios'],
             ],
             'children' => [
                 ['slug' => 'mens-clothing',   'depth' => 1, 'sort' => 1, 'translations' => ['en' => ["Men's Clothing", null], 'de' => ['Herrenkleidung', null], 'ar' => ['ملابس رجالية', null], 'fr' => ['Vêtements Hommes', null]]],
                 ['slug' => 'womens-clothing', 'depth' => 1, 'sort' => 2, 'translations' => ['en' => ["Women's Clothing", null], 'de' => ['Damenkleidung', null], 'ar' => ['ملابس نسائية', null], 'fr' => ['Vêtements Femmes', null]]],
                 ['slug' => 'kids-clothing',   'depth' => 1, 'sort' => 3, 'translations' => ['en' => ["Kids' Clothing", null], 'ar' => ['ملابس أطفال', null]]],
                 ['slug' => 'footwear',        'depth' => 1, 'sort' => 4, 'translations' => ['en' => ['Footwear', null], 'de' => ['Schuhe', null], 'ar' => ['أحذية', null], 'fr' => ['Chaussures', null]]],
             ],
            ],
            ['slug' => 'books', 'depth' => 0, 'sort' => 3,
             'translations' => [
                 'en' => ['Books', 'Physical and digital books'],
                 'ar' => ['كتب', 'كتب ورقية ورقمية'],
                 'de' => ['Bücher', 'Gedruckte und digitale Bücher'],
                 'fr' => ['Livres', 'Livres physiques et numériques'],
             ],
             'children' => [
                 ['slug' => 'fiction',     'depth' => 1, 'sort' => 1, 'translations' => ['en' => ['Fiction', null], 'ar' => ['الخيال', null], 'de' => ['Belletristik', null]]],
                 ['slug' => 'non-fiction', 'depth' => 1, 'sort' => 2, 'translations' => ['en' => ['Non-Fiction', null], 'ar' => ['غير الخيالي', null], 'de' => ['Sachbücher', null]]],
                 ['slug' => 'academic',    'depth' => 1, 'sort' => 3, 'translations' => ['en' => ['Academic', null], 'ar' => ['أكاديمي', null]]],
             ],
            ],
            ['slug' => 'home-kitchen', 'depth' => 0, 'sort' => 4,
             'translations' => ['en' => ['Home & Kitchen', null], 'de' => ['Haus & Küche', null], 'ar' => ['المنزل والمطبخ', null]],
             'children' => [],
            ],
            ['slug' => 'sports-outdoors', 'depth' => 0, 'sort' => 5,
             'translations' => ['en' => ['Sports & Outdoors', null], 'de' => ['Sport & Outdoor', null], 'ar' => ['الرياضة والهواء الطلق', null]],
             'children' => [],
            ],
        ];

        $this->insertTree($tree, null);

        $this->command->info('  Categories seeded');
    }

    private function insertTree(array $nodes, ?string $parentId): void
    {
        foreach ($nodes as $node) {
            $category = Category::create([
                'parent_id'  => $parentId,
                'slug'       => $node['slug'],
                'depth'      => $node['depth'],
                'sort_order' => $node['sort'],
                'is_active'  => true,
            ]);

            foreach ($node['translations'] as $lang => [$name, $desc]) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'lang_code'   => $lang,
                    'name'        => $name,
                    'description' => $desc,
                ]);
            }

            if (! empty($node['children'])) {
                $this->insertTree($node['children'], $category->id);
            }
        }
    }
}
