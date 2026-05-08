<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        User::role('buyer')->take(10)->get()->each(function ($buyer) {
            Wishlist::factory()->create(['user_id' => $buyer->id]);
        });

        $this->command->info('  Wishlists seeded');
    }
}
