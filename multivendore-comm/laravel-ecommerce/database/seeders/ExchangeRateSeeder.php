<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('exchange_rates')->truncate();

        $rates = [
            ['USD', 'INR', 83.52],
            ['USD', 'EUR', 0.92],
            ['USD', 'GBP', 0.79],
            ['USD', 'AED', 3.67],
            ['USD', 'JPY', 153.40],
            ['USD', 'CNY', 7.24],
            ['USD', 'SAR', 3.75],
            ['USD', 'BRL', 4.97],
            ['USD', 'CAD', 1.36],
            ['EUR', 'INR', 90.78],
            ['EUR', 'GBP', 0.86],
            ['GBP', 'INR', 105.59],
        ];

        foreach ($rates as [$from, $to, $rate]) {
            DB::table('exchange_rates')->insert([
                'id'            => Str::uuid(),
                'from_currency' => $from,
                'to_currency'   => $to,
                'rate'          => $rate,
                'fetched_at'    => now(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $this->command->info('  Exchange rates seeded (12)');
    }
}
