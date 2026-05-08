<?php
namespace Database\Factories;
use App\Models\Review;
use App\Models\ReviewMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewMediaFactory extends Factory
{
    protected $model = ReviewMedia::class;

    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'url'       => 'https://cdn.example.com/reviews/' . fake()->uuid() . '.jpg',
            'type'      => 'image',
        ];
    }
}
