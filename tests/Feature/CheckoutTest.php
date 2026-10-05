<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 1',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => 'Menteng',
            'postal_code' => '10310',
            'payment_method' => 'bank_transfer',
        ], $overrides);
    }

    public function test_checkout_creates_an_order_deducts_stock_and_empties_the_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 50000, 'stock' => 10]);
        $cart->products()->attach($product->id, ['quantity' => 2, 'price' => 50000]);

        $response = $this->actingAs($user)->post(route('checkout.process'), $this->checkoutPayload());

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order, 'Checkout should have created an order.');
        $response->assertRedirect(route('checkout.complete', $order->id));

        $this->assertSame(8, $product->fresh()->stock); // 10 - 2
        $this->assertSame(2, $product->fresh()->sold_count);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(0, $cart->fresh()->products()->count(), 'Cart should be emptied after checkout.');
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
    }

    public function test_checkout_is_rejected_when_the_cart_is_empty(): void
    {
        $user = User::factory()->create();
        Cart::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('checkout.process'), $this->checkoutPayload());

        $response->assertRedirect(route('cart.show'));
        $this->assertSame(0, Order::count());
    }

    public function test_checkout_is_rejected_when_requested_quantity_exceeds_stock(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 50000, 'stock' => 1]);
        // Cart quantity itself doesn't get re-checked against live stock
        // until checkout — this simulates stock having dropped (e.g.
        // another buyer checked out first) after it was added to cart.
        $cart->products()->attach($product->id, ['quantity' => 3, 'price' => 50000]);

        $response = $this->actingAs($user)->post(route('checkout.process'), $this->checkoutPayload());

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(0, Order::count());
        $this->assertSame(1, $product->fresh()->stock, 'Stock must be untouched when checkout is rejected.');
    }

    public function test_checkout_is_rejected_for_a_product_that_is_no_longer_active(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->inactive()->create(['price' => 50000, 'stock' => 10]);
        $cart->products()->attach($product->id, ['quantity' => 1, 'price' => 50000]);

        $response = $this->actingAs($user)->post(route('checkout.process'), $this->checkoutPayload());

        $response->assertSessionHas('error');
        $this->assertSame(0, Order::count());
        $this->assertSame(10, $product->fresh()->stock);
    }

    /**
     * The classic race condition for stock-limited checkout: two different
     * buyers both have the last unit of the same product in their cart and
     * both submit checkout. PHPUnit runs these sequentially rather than
     * truly concurrently, but this still exactly exercises the atomic
     * `UPDATE ... WHERE stock >= quantity` guard in
     * CheckoutController::process() that protects against the real
     * concurrent case — the second checkout must see the row already
     * consumed and fail cleanly, not oversell.
     */
    public function test_two_buyers_racing_for_the_last_unit_only_one_succeeds(): void
    {
        $product = Product::factory()->create(['price' => 50000, 'stock' => 1]);

        $firstBuyer = User::factory()->create();
        $firstCart = Cart::factory()->create(['user_id' => $firstBuyer->id]);
        $firstCart->products()->attach($product->id, ['quantity' => 1, 'price' => 50000]);

        $secondBuyer = User::factory()->create();
        $secondCart = Cart::factory()->create(['user_id' => $secondBuyer->id]);
        $secondCart->products()->attach($product->id, ['quantity' => 1, 'price' => 50000]);

        $firstResponse = $this->actingAs($firstBuyer)->post(route('checkout.process'), $this->checkoutPayload());
        $secondResponse = $this->actingAs($secondBuyer)->post(route('checkout.process'), $this->checkoutPayload());

        $this->assertSame(1, Order::count(), 'Exactly one of the two checkouts should have succeeded.');
        $this->assertSame(0, $product->fresh()->stock);

        $successfulOrder = Order::first();
        $this->assertContains($successfulOrder->user_id, [$firstBuyer->id, $secondBuyer->id]);

        // Whichever buyer lost the race should have been sent back with an
        // error and still have their item sitting in their cart.
        $loserCart = $successfulOrder->user_id === $firstBuyer->id ? $secondCart : $firstCart;
        $this->assertSame(1, $loserCart->fresh()->products()->count());
    }
}
