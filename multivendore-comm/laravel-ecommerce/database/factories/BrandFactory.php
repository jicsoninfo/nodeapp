<?php
namespace Database\Factories;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();
        return [
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . fake()->numberBetween(1, 9999),
            'logo_url'    => 'https://cdn.example.com/brands/' . fake()->uuid() . '.svg',
            'is_verified' => fake()->boolean(70),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => ['is_verified' => true]);
    }
}
