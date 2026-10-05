<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'product_id' => ProductFactory::new(),
            'score' => fake()->numberBetween(1, 5),
            'review' => fake()->sentence(),
            'verified_purchase' => false,
            'is_approved' => false,
        ];
    }

    /**
     * Matches what RatingController::store() itself sets for a rating from
     * a buyer whose order for that product has actually been delivered.
     */
    public function verifiedPurchase(): static
    {
        return $this->state(fn () => [
            'verified_purchase' => true,
            'is_approved' => true,
        ]);
    }
}
