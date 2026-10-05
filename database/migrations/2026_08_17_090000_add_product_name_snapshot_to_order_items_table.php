<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * order_items never stored the product's name, and OrderItem::product()
     * didn't use withTrashed() either — so once a product was soft-deleted,
     * every past order referencing it permanently lost the product name
     * (the "orders/show" view fell back to "Produk tidak tersedia").
     *
     * This adds a point-in-time snapshot column and backfills it from
     * whatever name each item's product currently has. DB::table() (query
     * builder) is used rather than the Eloquent Product model so the
     * lookup below is NOT filtered by the SoftDeletes global scope —
     * soft-deleted products still get their name backfilled here, since
     * this is the last moment that name is still recoverable at all.
     *
     * Backfilling via a chunked loop of plain where()->update() calls
     * (rather than a single UPDATE ... JOIN) is deliberate: JOIN syntax in
     * an UPDATE isn't portable — SQLite doesn't support it at all, and the
     * exact syntax differs between MySQL and Postgres — so this keeps the
     * migration (and the SQLite-backed test suite) driver-agnostic.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
        });

        DB::table('products')->select('id', 'name')->orderBy('id')->chunk(200, function ($products) {
            foreach ($products as $product) {
                DB::table('order_items')
                    ->where('product_id', $product->id)
                    ->update(['product_name' => $product->name]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
        });
    }
};
