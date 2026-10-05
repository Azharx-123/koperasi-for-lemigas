<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verifying_a_confirmation_on_a_cancelled_order_is_blocked(): void
    {
        $order = Order::factory()->cancelled()->create(['total' => 100000]);
        $confirmation = PaymentConfirmation::factory()->create([
            'order_id' => $order->id,
            'amount' => 100000,
        ]);

        try {
            $confirmation->verify();
            $this->fail('Expected a RuntimeException because the order is cancelled.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('tidak bisa diverifikasi', $e->getMessage());
        }

        $this->assertNull($confirmation->fresh()->verified_at);
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_verifying_a_confirmation_with_a_mismatched_amount_is_blocked(): void
    {
        $order = Order::factory()->create(['status' => 'pending', 'total' => 100000]);
        $confirmation = PaymentConfirmation::factory()->create([
            'order_id' => $order->id,
            'amount' => 95000, // buyer transferred less than the order total
        ]);

        try {
            $confirmation->verify();
            $this->fail('Expected a RuntimeException for the amount mismatch.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('tidak sama dengan', $e->getMessage());
        }

        $this->assertNull($confirmation->fresh()->verified_at);
        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_verifying_a_matching_confirmation_marks_the_order_paid_and_processing(): void
    {
        $order = Order::factory()->create(['status' => 'pending', 'payment_status' => 'pending', 'total' => 100000]);
        $confirmation = PaymentConfirmation::factory()->create([
            'order_id' => $order->id,
            'amount' => 100000,
        ]);

        $confirmation->verify();

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($confirmation->fresh()->verified_at);
    }

    public function test_verifying_an_already_verified_confirmation_is_a_harmless_no_op(): void
    {
        // Cancelled deliberately: allowedNextStatuses() would reject this if
        // the is_verified short-circuit in verify() didn't run first — this
        // proves the idempotency check really does come before that gate,
        // not just that it happens to not matter for a still-open order.
        $order = Order::factory()->cancelled()->create(['total' => 100000]);
        $confirmation = PaymentConfirmation::factory()->create([
            'order_id' => $order->id,
            'amount' => 100000,
            'verified_at' => now()->subDay(),
        ]);

        // Should not throw despite the order being cancelled.
        $confirmation->verify();

        $this->assertTrue(true); // reaching here means no exception was thrown
    }
}
