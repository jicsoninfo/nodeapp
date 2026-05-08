<?php

namespace Database\Factories;

use App\Enums\FulfillmentStatus;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $variant = ProductVariant::inRandomOrder()->first();

        return [
            'vendor_id'          => $variant?->product?->vendor_id ?? Vendor::inRandomOrder()->first()?->id,
            'variant_id'         => $variant?->id,
            'quantity'           => fake()->numberBetween(1, 5),
            'unit_price'         => $variant?->price ?? fake()->randomFloat(2, 10, 500),
            'tax_rate'           => fake()->randomElement([0, 5, 9, 18]),
            'fulfillment_status' => FulfillmentStatus::Delivered,
        ];
    }
}
