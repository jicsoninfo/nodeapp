<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::inRandomOrder()->take(20)->get();
        $buyers   = User::role('buyer')->get();

        foreach ($products as $product) {
            $reviewers = $buyers->random(rand(2, 5));
            foreach ($reviewers as $buyer) {
                Review::factory()->create([
                    'product_id' => $product->id,
                    'user_id'    => $buyer->id,
                    'status'     => 'approved',
                ]);
            }
        }

        $this->command->info('  Reviews seeded (' . Review::count() . ' total)');
    }
}
