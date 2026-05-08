<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'method'         => fake()->randomElement([PaymentMethod::Card, PaymentMethod::UPI, PaymentMethod::COD]),
            'provider'       => fake()->randomElement(['stripe', 'razorpay', 'manual']),
            'provider_txn_id'=> 'TXN_' . strtoupper(fake()->bothify('??########')),
            'amount'         => fake()->randomFloat(2, 10, 2000),
            'currency'       => fake()->randomElement(['USD', 'INR', 'EUR']),
            'status'         => PaymentStatus::Captured,
            'processed_at'   => now()->subMinutes(rand(1, 1440)),
            'meta'           => [],
        ];
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => PaymentStatus::Failed, 'processed_at' => null]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => PaymentStatus::Pending, 'processed_at' => null]);
    }
}
