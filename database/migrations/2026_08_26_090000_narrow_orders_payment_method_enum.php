<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Narrows orders.payment_method to match Order::PAYMENT_METHODS, which
     * has only ever listed 'bank_transfer' — no gateway integration exists
     * for 'credit_card' or 'ewallet', and CheckoutController::process()'s
     * validation never accepts them from the checkout form either. Keeping
     * them in the schema just let the database accept values the
     * application layer can never actually produce or handle.
     */
    public function up(): void
    {
        // Defensive, same pattern as the unique-constraint migrations
        // above: fold any stray non-bank_transfer rows in before
        // narrowing the enum. Nothing in the app ever writes
        // credit_card/ewallet, so this should be a no-op in practice, but
        // it keeps this migration safe to run on any existing database.
        DB::table('orders')
            ->where('payment_method', '!=', 'bank_transfer')
            ->update(['payment_method' => 'bank_transfer']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['bank_transfer'])
                ->default('bank_transfer')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'ewallet'])
                ->default('bank_transfer')
                ->change();
        });
    }
};
