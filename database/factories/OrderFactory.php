<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(50, 500) * 1000;
        $shipping = 25000;
        $tax = round($subtotal * \App\Models\Order::TAX_RATE, 2);

        return [
            'user_id' => UserFactory::new(),
            // Same format CheckoutController::process() actually generates,
            // so factory-made orders look like real ones.
            'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . Str::random(6),
            'status' => 'pending',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $subtotal + $shipping + $tax,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->address(),
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => fake()->citySuffix(),
            'postal_code' => fake()->numerify('#####'),
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
        ];
    }

    public function processing(): static
    {
        return $this->state(fn () => ['status' => 'processing']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }

    public function paid(): static
    {
        return $this->state(fn () => ['payment_status' => 'paid']);
    }
}
