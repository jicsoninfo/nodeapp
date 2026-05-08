<?php
namespace Database\Factories;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [
            'parent_id'  => null,
            'slug'       => Str::slug($name) . '-' . fake()->numberBetween(1, 9999),
            'depth'      => 0,
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active'  => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Category $category) {
            CategoryTranslation::create([
                'category_id' => $category->id,
                'lang_code'   => 'en',
                'name'        => ucwords(str_replace('-', ' ', $category->slug)),
                'description' => null,
            ]);
        });
    }

    public function child(Category $parent): static
    {
        return $this->state(fn () => ['parent_id' => $parent->id, 'depth' => $parent->depth + 1]);
    }
}
