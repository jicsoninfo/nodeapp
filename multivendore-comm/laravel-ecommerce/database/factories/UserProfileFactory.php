<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        $locales = ['en', 'hi', 'ar', 'de', 'fr', 'zh', 'ja', 'es'];

        return [
            'first_name'   => fake()->firstName(),
            'last_name'    => fake()->lastName(),
            'avatar_url'   => fake()->boolean(30) ? "https://cdn.example.com/avatars/" . fake()->uuid() . ".jpg" : null,
            'locale'       => fake()->randomElement($locales),
            'timezone'     => fake()->timezone(),
            'date_of_birth'=> fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
        ];
    }
}
