<?php
namespace Database\Factories;
use App\Models\VendorProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorProfileFactory extends Factory
{
    protected $model = VendorProfile::class;

    public function definition(): array
    {
        return [
            'description'   => fake()->paragraph(3),
            'logo_url'      => 'https://cdn.example.com/vendors/logo-' . fake()->uuid() . '.png',
            'banner_url'    => 'https://cdn.example.com/vendors/banner-' . fake()->uuid() . '.jpg',
            'business_type' => fake()->randomElement(['individual', 'company', 'brand']),
            'tax_id'        => strtoupper(fake()->bothify('??##########')),
            'avg_rating'    => fake()->randomFloat(2, 3.5, 5.0),
            'total_reviews' => fake()->numberBetween(0, 50000),
            'website_url'   => fake()->boolean(60) ? fake()->url() : null,
        ];
    }
}
