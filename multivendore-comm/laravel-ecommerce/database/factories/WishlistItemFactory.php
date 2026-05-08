<?php
namespace Database\Factories;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistItemFactory extends Factory
{
    protected $model = WishlistItem::class;

    public function definition(): array
    {
        return [
            'wishlist_id' => Wishlist::factory(),
            'variant_id'  => ProductVariant::factory(),
            'added_at'    => fake()->dateTimeBetween('-60 days', 'now'),
        ];
    }
}
