<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductTranslation;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'vendor_id'    => Vendor::inRandomOrder()->first()?->id ?? Vendor::factory(),
            'category_id'  => Category::where('depth', '>', 0)->inRandomOrder()->first()?->id,
            'brand_id'     => Brand::inRandomOrder()->first()?->id,
            'asin'         => strtoupper(Str::random(10)),
            'status'       => fake()->randomElement([ProductStatus::Active, ProductStatus::Active, ProductStatus::Draft]),
            'avg_rating'   => fake()->randomFloat(2, 3.0, 5.0),
            'total_reviews'=> fake()->numberBetween(0, 5000),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            // English translation (always)
            ProductTranslation::create([
                'product_id'        => $product->id,
                'lang_code'         => 'en',
                'name'              => fake()->words(rand(3, 7), true),
                'description'       => fake()->paragraphs(3, true),
                'short_description' => fake()->sentence(10),
                'meta_title'        => fake()->words(5, true),
                'meta_description'  => fake()->sentence(15),
            ]);

            // Random second language
            $secondLang = fake()->randomElement(['hi', 'ar', 'de', 'fr', 'zh', 'ja']);
            ProductTranslation::create([
                'product_id'        => $product->id,
                'lang_code'         => $secondLang,
                'name'              => fake()->words(rand(3, 7), true),
                'description'       => fake()->paragraphs(2, true),
                'short_description' => fake()->sentence(8),
            ]);

            // 1-3 variants
            $variantCount = rand(1, 3);
            for ($i = 0; $i < $variantCount; $i++) {
                ProductVariant::factory()->create(['product_id' => $product->id]);
            }

            // 1-4 media
            for ($i = 0; $i < rand(1, 4); $i++) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'url'        => "https://cdn.example.com/products/{$product->id}/img_{$i}.jpg",
                    'type'       => 'image',
                    'alt_text'   => "Product image {$i}",
                    'sort_order' => $i,
                ]);
            }
        });
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => ProductStatus::Active]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ProductStatus::Draft]);
    }
}
