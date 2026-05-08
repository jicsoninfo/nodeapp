<?php
namespace Database\Factories;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTranslationFactory extends Factory
{
    protected $model = ProductTranslation::class;

    public function definition(): array
    {
        return [
            'product_id'        => Product::factory(),
            'lang_code'         => 'en',
            'name'              => fake()->words(rand(3, 7), true),
            'description'       => fake()->paragraphs(2, true),
            'short_description' => fake()->sentence(10),
            'meta_title'        => fake()->words(5, true),
            'meta_description'  => fake()->sentence(15),
        ];
    }
}
