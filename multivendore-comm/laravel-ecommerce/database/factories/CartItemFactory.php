<?php
namespace Database\Factories;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $variant = ProductVariant::factory()->create();
        return [
            'cart_id'    => Cart::factory(),
            'variant_id' => $variant->id,
            'vendor_id'  => Vendor::factory(),
            'quantity'   => fake()->numberBetween(1, 5),
            'unit_price' => fake()->randomFloat(2, 5, 500),
        ];
    }
}
