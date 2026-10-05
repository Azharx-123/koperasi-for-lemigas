<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelling_an_order_restores_each_items_stock(): void
    {
        $productA = Product::factory()->create(['stock' => 10]);
        $productB = Product::factory()->create(['stock' => 5]);
        $order = Order::factory()->processing()->create();

        // Creating these through the factory (not withoutAutoSync) deducts
        // stock via OrderItem::boot(), same as an admin adding items would —
        // so $productA/$productB already reflect "3 sold" / "2 sold" here.
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $productA->id, 'quantity' => 3, 'price' => $productA->price]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $productB->id, 'quantity' => 2, 'price' => $productB->price]);

        $this->assertSame(7, $productA->fresh()->stock);
        $this->assertSame(3, $productB->fresh()->stock);

        $order->cancelAndRestoreStock();

        $this->assertSame(10, $productA->fresh()->stock);
        $this->assertSame(5, $productB->fresh()->stock);
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    /**
     * Cancelling an order must reset a pending/processing payment_status
     * to "failed", so a cancelled order can't permanently show
     * "Dibatalkan" next to "Memproses Pembayaran".
     */
    public function test_cancelling_resets_a_pending_or_processing_payment_status_to_failed(): void
    {
        $order = Order::factory()->processing()->create(['payment_status' => 'processing']);

        $order->cancelAndRestoreStock();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('failed', $order->fresh()->payment_status);
    }

    /**
     * A 'paid' order is a refund scenario, not something
     * cancelAndRestoreStock() should reclassify as a failed payment.
     */
    public function test_cancelling_a_paid_order_leaves_payment_status_alone(): void
    {
        $order = Order::factory()->processing()->paid()->create();

        $order->cancelAndRestoreStock();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_cancelling_reverses_each_products_sold_count(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'sold_count' => 3]);
        $order = Order::factory()->processing()->create();
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 3, 'price' => $product->price]);

        // sold_count is now 6 (3 pre-existing + 3 from the item above)
        $this->assertSame(6, $product->fresh()->sold_count);

        $order->cancelAndRestoreStock();

        $this->assertSame(3, $product->fresh()->sold_count);
    }
}
