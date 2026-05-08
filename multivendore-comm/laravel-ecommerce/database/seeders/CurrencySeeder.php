<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('currencies')->truncate();

        DB::table('currencies')->insert([
            ['code' => 'USD', 'name' => 'US Dollar',       'symbol' => '$',   'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EUR', 'name' => 'Euro',            'symbol' => '€',   'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'INR', 'name' => 'Indian Rupee',    'symbol' => '₹',   'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'GBP', 'name' => 'British Pound',   'symbol' => '£',   'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'JPY', 'name' => 'Japanese Yen',    'symbol' => '¥',   'decimal_places' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'AED', 'name' => 'UAE Dirham',      'symbol' => 'د.إ', 'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CNY', 'name' => 'Chinese Yuan',    'symbol' => '¥',   'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SAR', 'name' => 'Saudi Riyal',     'symbol' => '﷼',  'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BRL', 'name' => 'Brazilian Real',  'symbol' => 'R$',  'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$',  'decimal_places' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('  Currencies seeded (10)');
    }
}
