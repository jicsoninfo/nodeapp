<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple',         'slug' => 'apple',         'logo_url' => 'https://cdn.example.com/brands/apple.svg',         'is_verified' => true],
            ['name' => 'Samsung',       'slug' => 'samsung',       'logo_url' => 'https://cdn.example.com/brands/samsung.svg',       'is_verified' => true],
            ['name' => 'OnePlus',       'slug' => 'oneplus',       'logo_url' => 'https://cdn.example.com/brands/oneplus.svg',       'is_verified' => true],
            ['name' => 'Sony',          'slug' => 'sony',          'logo_url' => 'https://cdn.example.com/brands/sony.svg',          'is_verified' => true],
            ['name' => 'Nike',          'slug' => 'nike',          'logo_url' => 'https://cdn.example.com/brands/nike.svg',          'is_verified' => true],
            ['name' => 'Adidas',        'slug' => 'adidas',        'logo_url' => 'https://cdn.example.com/brands/adidas.svg',        'is_verified' => true],
            ["name" => "Levi's",        'slug' => 'levis',         'logo_url' => "https://cdn.example.com/brands/levis.svg",         'is_verified' => true],
            ['name' => 'Penguin Books', 'slug' => 'penguin-books', 'logo_url' => 'https://cdn.example.com/brands/penguin.svg',       'is_verified' => true],
            ['name' => 'Anker',         'slug' => 'anker',         'logo_url' => 'https://cdn.example.com/brands/anker.svg',         'is_verified' => true],
            ['name' => 'Xiaomi',        'slug' => 'xiaomi',        'logo_url' => 'https://cdn.example.com/brands/xiaomi.svg',        'is_verified' => true],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        $this->command->info('  Brands seeded (10)');
    }
}
