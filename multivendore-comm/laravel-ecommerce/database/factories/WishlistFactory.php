<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistFactory extends Factory
{
    protected $model = Wishlist::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->randomElement(['My Wishlist', 'Birthday List', 'Gift Ideas', 'Tech Wants', 'Next Purchase']),
            'is_public' => fake()->boolean(20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Wishlist $wishlist) {
            $variants = ProductVariant::inRandomOrder()->take(rand(1, 8))->get();
            foreach ($variants as $variant) {
                WishlistItem::create([
                    'wishlist_id' => $wishlist->id,
                    'variant_id'  => $variant->id,
                    'added_at'    => now()->subDays(rand(0, 60)),
                ]);
            }
        });
    }
}
