<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentConfirmation>
 */
class PaymentConfirmationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => OrderFactory::new(),
            'bank_name' => 'Bank Mandiri',
            'account_name' => fake()->name(),
            'amount' => fake()->numberBetween(50, 500) * 1000,
            'transfer_date' => now()->toDateString(),
            'proof_image' => 'payment-proofs/fake-proof.jpg',
        ];
    }
}
