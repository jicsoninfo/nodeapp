<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::role('buyer')->take(8)->get();
        foreach ($buyers as $buyer) {
            Order::factory(rand(1, 5))->create(['user_id' => $buyer->id]);
        }

        $this->command->info('  Orders seeded (' . Order::count() . ' total)');
    }
}
