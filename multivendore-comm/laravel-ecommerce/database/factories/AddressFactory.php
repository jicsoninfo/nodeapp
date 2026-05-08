<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'label'       => fake()->randomElement(['Home', 'Work', 'Other']),
            'full_name'   => fake()->name(),
            'line1'       => fake()->streetAddress(),
            'line2'       => fake()->boolean(30) ? fake()->secondaryAddress() : null,
            'city'        => fake()->city(),
            'state'       => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country_code'=> fake()->randomElement(['US', 'IN', 'DE', 'AE', 'GB', 'FR', 'JP']),
            'is_default'  => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
