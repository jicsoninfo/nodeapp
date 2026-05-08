<?php
namespace Database\Factories;
use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = fake()->randomElement(CouponType::cases());
        return [
            'code'        => strtoupper(Str::random(8)),
            'type'        => $type,
            'value'       => $type === CouponType::Percent ? fake()->numberBetween(5, 50) : fake()->randomFloat(2, 5, 100),
            'min_order'   => fake()->boolean(60) ? fake()->randomFloat(2, 10, 200) : null,
            'usage_limit' => fake()->boolean(70) ? fake()->numberBetween(50, 1000) : null,
            'used_count'  => 0,
            'vendor_id'   => null,
            'expires_at'  => fake()->boolean(80) ? now()->addMonths(fake()->numberBetween(1, 12)) : null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => now()->subDay()]);
    }

    public function limitReached(): static
    {
        return $this->state(fn () => ['usage_limit' => 10, 'used_count' => 10]);
    }
}
