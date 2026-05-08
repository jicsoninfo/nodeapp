<?php
namespace Database\Factories;
use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    public function definition(): array
    {
        return [
            'from_currency' => 'USD',
            'to_currency'   => fake()->randomElement(['EUR', 'INR', 'GBP', 'AED', 'JPY']),
            'rate'          => fake()->randomFloat(4, 0.5, 150),
            'fetched_at'    => now(),
        ];
    }
}
