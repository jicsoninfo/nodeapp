<?php
namespace Database\Factories;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserNotificationFactory extends Factory
{
    protected $model = UserNotification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type'    => fake()->randomElement(['order_placed','order_shipped','order_delivered','price_drop','new_review']),
            'channel' => fake()->randomElement(['in_app','email','push']),
            'title'   => fake()->sentence(5),
            'body'    => fake()->sentence(15),
            'data'    => [],
            'is_read' => fake()->boolean(40),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn () => ['is_read' => false, 'read_at' => null]);
    }

    public function read(): static
    {
        return $this->state(fn () => ['is_read' => true, 'read_at' => now()]);
    }
}
