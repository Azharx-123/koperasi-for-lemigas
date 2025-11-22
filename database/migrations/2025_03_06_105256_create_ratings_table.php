<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrasi untuk tabel ratings
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedTinyInteger('score')->comment('1-5 stars');
            $table->text('review')->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            // Satu user hanya bisa memberikan satu rating per produk
            $table->unique(['user_id', 'product_id']);

            // Indeks untuk meningkatkan performa query
            $table->index(['product_id', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
