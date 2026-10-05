<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 *
 * Plain factory — deliberately NOT wrapped in OrderItem::withoutAutoSync().
 * Creating an item through this factory goes through the normal boot()
 * hooks (see OrderItem::boot()), so it realistically deducts the related
 * product's stock and recalculates the parent order's totals, same as an
 * admin adding an item via ItemsRelationManager would. Tests that need to
 * exercise CheckoutController's own atomic stock deduction should go
 * through the actual checkout.process route instead of this factory.
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 3);
        $price = fake()->numberBetween(10, 200) * 1000;

        return [
            'order_id' => OrderFactory::new(),
            'product_id' => ProductFactory::new(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $price * $quantity,
        ];
    }
}
