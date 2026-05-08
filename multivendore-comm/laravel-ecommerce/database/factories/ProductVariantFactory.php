<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $price     = fake()->randomFloat(2, 9.99, 1999.99);
        $hasSale   = fake()->boolean(30);
        $currencies = ['USD', 'INR', 'EUR', 'AED'];

        return [
            'sku'            => strtoupper(Str::random(3)) . '-' . fake()->numerify('######'),
            'price'          => $price,
            'sale_price'     => $hasSale ? round($price * fake()->randomFloat(2, 0.70, 0.90), 2) : null,
            'currency'       => fake()->randomElement($currencies),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'weight_grams'   => fake()->numberBetween(50, 5000),
            'is_active'      => fake()->boolean(90),
        ];
    }

    public function inStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => rand(10, 100), 'is_active' => true]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => 0]);
    }
}
