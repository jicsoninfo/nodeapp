<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    private static array $titles = [
        'en' => ['Absolutely love it!', 'Great product', 'Exceeded expectations', 'Would buy again', 'Good value for money', 'Not bad overall'],
        'ar' => ['ممتاز جداً', 'منتج رائع', 'تجاوز توقعاتي'],
        'de' => ['Sehr gut!', 'Empfehlenswert', 'Tolles Produkt'],
        'fr' => ['Excellent produit', 'Très satisfait', 'Je recommande'],
        'hi' => ['बहुत बढ़िया', 'शानदार उत्पाद', 'पैसा वसूल'],
    ];

    public function definition(): array
    {
        $langs  = array_keys(self::$titles);
        $lang   = fake()->randomElement($langs);
        $titles = self::$titles[$lang];

        return [
            'rating'               => fake()->numberBetween(3, 5),
            'title'                => fake()->randomElement($titles),
            'body'                 => fake()->paragraphs(rand(1, 3), true),
            'is_verified_purchase' => fake()->boolean(60),
            'helpful_votes'        => fake()->numberBetween(0, 200),
            'status'               => 'approved',
            'lang_code'            => $lang,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function negative(): static
    {
        return $this->state(fn () => ['rating' => fake()->numberBetween(1, 2)]);
    }
}
