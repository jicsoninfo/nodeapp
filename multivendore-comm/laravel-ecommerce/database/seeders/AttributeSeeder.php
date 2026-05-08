<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeTranslation;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Color', 'type' => 'color',
                'translations' => ['hi' => 'रंग', 'ar' => 'اللون', 'de' => 'Farbe', 'fr' => 'Couleur', 'es' => 'Color', 'zh' => '颜色'],
                'values' => ['Black', 'White', 'Blue', 'Red', 'Green', 'Gold', 'Silver', 'Titanium Black', 'Titanium Natural'],
            ],
            [
                'name' => 'Storage', 'type' => 'select',
                'translations' => ['hi' => 'स्टोरेज', 'ar' => 'التخزين', 'de' => 'Speicher'],
                'values' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
            ],
            [
                'name' => 'RAM', 'type' => 'select',
                'translations' => ['ar' => 'ذاكرة الوصول العشوائي', 'zh' => '内存'],
                'values' => ['4GB', '8GB', '12GB', '16GB', '18GB', '32GB', '36GB'],
            ],
            [
                'name' => 'Size', 'type' => 'size',
                'translations' => ['hi' => 'आकार', 'ar' => 'المقاس', 'de' => 'Größe', 'fr' => 'Taille', 'es' => 'Talla'],
                'values' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '38', '39', '40', '41', '42', '43', '44', '45'],
            ],
            [
                'name' => 'Material', 'type' => 'text',
                'translations' => ['ar' => 'المادة', 'de' => 'Material', 'fr' => 'Matière'],
                'values' => ['Cotton', 'Polyester', 'Leather', 'Titanium', 'Aluminum', 'Plastic', 'Rubber'],
            ],
            [
                'name' => 'Connectivity', 'type' => 'select',
                'translations' => ['ar' => 'الاتصال', 'de' => 'Konnektivität'],
                'values' => ['Wi-Fi', 'Bluetooth', '5G', '4G LTE', 'USB-C', 'Lightning'],
            ],
        ];

        foreach ($attributes as $i => $def) {
            $attr = Attribute::create(['name' => $def['name'], 'type' => $def['type']]);

            foreach ($def['translations'] as $lang => $label) {
                AttributeTranslation::create(['attribute_id' => $attr->id, 'lang_code' => $lang, 'label' => $label]);
            }

            foreach ($def['values'] as $sort => $value) {
                AttributeValue::create(['attribute_id' => $attr->id, 'value' => $value, 'sort_order' => $sort + 1]);
            }
        }

        $this->command->info('  Attributes seeded (6 attributes with values)');
    }
}
