<?php
namespace Database\Factories;
use App\Enums\PlanType;
use App\Enums\VendorStatus;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();
        return [
            'owner_user_id'  => User::factory(),
            'store_name'     => $name,
            'slug'           => Str::slug($name),
            'status'         => VendorStatus::Active,
            'plan_type'      => fake()->randomElement(PlanType::cases()),
            'commission_rate'=> fake()->randomFloat(2, 8, 20),
            'approved_at'    => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => VendorStatus::Pending, 'approved_at' => null]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => VendorStatus::Suspended]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => VendorStatus::Active, 'approved_at' => now()]);
    }
}
