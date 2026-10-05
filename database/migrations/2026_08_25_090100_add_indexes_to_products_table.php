<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ProductController::index() always filters ->active() (status =
        // 'active') and then sorts by one of created_at (default/"latest"),
        // price (price_low/price_high), or sold_count (popular) — status
        // had no index of its own, so every one of those four listing
        // queries was a full table scan. These three composites cover
        // exactly those filter+sort pairs. Not urgent at today's catalog
        // size, but cheap to add now and worth having before it grows.
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index(['status', 'price']);
            $table->index(['status', 'sold_count']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['status', 'price']);
            $table->dropIndex(['status', 'sold_count']);
        });
    }
};
