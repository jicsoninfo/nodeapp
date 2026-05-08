<?php
namespace Database\Factories;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'session_id' => null,
            'currency'   => fake()->randomElement(['USD', 'INR', 'EUR']),
            'expires_at' => now()->addDays(7),
        ];
    }

    public function guest(): static
    {
        return $this->state(fn () => [
            'user_id'    => null,
            'session_id' => 'sess_' . fake()->uuid(),
            'expires_at' => now()->addHours(24),
        ]);
    }
}
