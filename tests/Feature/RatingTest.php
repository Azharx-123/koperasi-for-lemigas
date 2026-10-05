<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_a_rating(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('ratings.store', $product), [
            'score' => 5,
            'review' => 'Bagus sekali',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertSame(0, Rating::count());
    }

    public function test_authenticated_user_can_submit_a_rating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('ratings.store', $product), [
            'score' => 4,
            'review' => 'Produknya oke, pengiriman cepat.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'score' => 4,
            'review' => 'Produknya oke, pengiriman cepat.',
        ]);
    }

    public function test_score_must_be_between_1_and_5(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('ratings.store', $product), [
            'score' => 6,
            'review' => 'Test',
        ]);

        $response->assertSessionHasErrors('score');
        $this->assertSame(0, Rating::count());
    }

    /**
     * RatingController::store() derives verified_purchase/is_approved from
     * whether the buyer has a *delivered* order containing this product —
     * simply owning an account isn't enough.
     */
    public function test_rating_from_a_non_purchaser_is_saved_but_not_approved(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->post(route('ratings.store', $product), [
            'score' => 3,
            'review' => 'Belum pernah beli, cuma coba-coba kasih rating.',
        ]);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'verified_purchase' => false,
            'is_approved' => false,
        ]);
    }

    public function test_rating_from_a_delivered_order_is_auto_approved(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        $this->actingAs($user)->post(route('ratings.store', $product), [
            'score' => 5,
            'review' => 'Sudah terima barangnya, mantap.',
        ]);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'verified_purchase' => true,
            'is_approved' => true,
        ]);
    }

    public function test_submitting_again_updates_the_existing_rating_instead_of_duplicating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->post(route('ratings.store', $product), [
            'score' => 2,
            'review' => 'Awalnya kurang puas.',
        ]);

        $this->actingAs($user)->post(route('ratings.store', $product), [
            'score' => 5,
            'review' => 'Setelah dipakai lagi, ternyata bagus. Update rating.',
        ]);

        $this->assertSame(1, Rating::where('user_id', $user->id)->where('product_id', $product->id)->count());
        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'score' => 5,
        ]);
    }

    public function test_index_only_lists_approved_ratings(): void
    {
        $product = Product::factory()->create();
        $approved = Rating::factory()->verifiedPurchase()->create([
            'product_id' => $product->id,
            'review' => 'Ulasan yang sudah disetujui',
        ]);
        $unapproved = Rating::factory()->create([
            'product_id' => $product->id,
            'review' => 'Ulasan yang belum disetujui',
        ]);

        $response = $this->get(route('ratings.index', $product));

        $response->assertOk();
        $response->assertSee($approved->review);
        $response->assertDontSee($unapproved->review);
    }
}
