<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_a_new_product_creates_a_cart_row_with_the_current_price(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 75000, 'stock' => 10]);

        $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 2]);

        $cart = Cart::where('user_id', $user->id)->first();
        $pivot = $cart->products()->first()->pivot;
        $this->assertSame(2, $pivot->quantity);
        $this->assertEquals(75000, $pivot->price);
    }

    /**
     * Adding a product that's already in the cart must refresh its price
     * to the product's current price, not leave it frozen at whatever it
     * was the first time the product was added.
     */
    public function test_adding_an_already_cart_ed_product_again_refreshes_the_price(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->products()->attach($product->id, ['quantity' => 1, 'price' => 50000]);

        // Admin raises the price after the customer already added it.
        $product->update(['price' => 60000]);

        $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 1]);

        $pivot = $cart->products()->first()->pivot;
        $this->assertSame(2, $pivot->quantity);
        $this->assertEquals(60000, $pivot->price, 'Price should follow the current product price, not the stale one from the first add.');
    }

    public function test_combined_quantity_exceeding_stock_is_rejected(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 5]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->products()->attach($product->id, ['quantity' => 4, 'price' => 50000]);

        $response = $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 3]);

        $response->assertSessionHas('error');
        $this->assertSame(4, $cart->fresh()->products()->first()->pivot->quantity, 'Quantity must be unchanged when the combined amount is rejected.');
    }

    public function test_inactive_product_cannot_be_added_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->inactive()->create(['stock' => 10]);

        $response = $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 1]);

        $response->assertSessionHas('error');
        $this->assertSame(0, Cart::where('user_id', $user->id)->first()?->products()->count() ?? 0);
    }

    /**
     * Safety net tested directly at the DB level: two pivot rows for the
     * same (cart_id, product_id) should never coexist, whatever route two
     * racing requests take to get there.
     */
    public function test_the_database_rejects_a_duplicate_cart_product_row(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        DB::table('cart_product')->insert([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        DB::table('cart_product')->insert([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
