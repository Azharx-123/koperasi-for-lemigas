<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Before this migration, nothing stopped two pivot rows existing for
        // the same (cart_id, product_id) — CartController::add()'s
        // "does it already exist" check and its attach() call aren't atomic.
        // Fold any such duplicates into one row before adding the
        // constraint, so this migration doesn't fail on an install where
        // that race already produced duplicate rows.
        $duplicates = DB::table('cart_product')
            ->select('cart_id', 'product_id')
            ->groupBy('cart_id', 'product_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $rows = DB::table('cart_product')
                ->where('cart_id', $duplicate->cart_id)
                ->where('product_id', $duplicate->product_id)
                ->orderBy('id')
                ->get();

            $keeper = $rows->first();

            DB::table('cart_product')->where('id', $keeper->id)->update([
                'quantity' => $rows->sum('quantity'),
                // Keep whichever row was touched most recently — closest to
                // the product's current price.
                'price' => $rows->sortByDesc('updated_at')->first()->price,
            ]);

            DB::table('cart_product')
                ->where('cart_id', $duplicate->cart_id)
                ->where('product_id', $duplicate->product_id)
                ->where('id', '!=', $keeper->id)
                ->delete();
        }

        Schema::table('cart_product', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_product', function (Blueprint $table) {
            $table->dropUnique(['cart_id', 'product_id']);
        });
    }
};
