<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        // Product::boot() derives 'slug' from 'name' itself on creating(),
        // so it's deliberately not set here.
        return [
            'name' => ucwords(fake()->unique()->words(3, true)),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(10, 500) * 1000,
            'stock' => 20,
            'category_id' => CategoryFactory::new(),
            'status' => 'active',
            'featured' => false,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
