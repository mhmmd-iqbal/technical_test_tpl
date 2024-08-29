<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => $this->faker->imageUrl(640, 480, 'products', true),
        ];
    }

    // Attach categories to a product after it's created
    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // Attach 1 to 3 random categories to each product
            $categories = Category::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $product->categories()->sync($categories);
        });
    }
}
