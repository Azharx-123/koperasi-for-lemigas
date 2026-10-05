<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Changes orders.user_id's foreign key from cascadeOnDelete() to
     * nullOnDelete(). ProfileController::destroy() only blocks account
     * deletion when *active* orders exist, which only makes sense if
     * historical (delivered/cancelled) orders are meant to survive as
     * business records — so deleting a user detaches their past orders
     * (user_id set to null) instead of destroying them.
     *
     * Uses Schema::table()->change(), which needs no doctrine/dbal on
     * Laravel 11.31+ (this project's floor — see composer.json), so this
     * migration runs on any driver Laravel supports, including the
     * SQLite-backed test suite.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Note: this will fail if any orders already have a null user_id
        // (i.e. their original account has since been deleted under the
        // new behavior) — those rows would need to be resolved manually
        // before rolling back.
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
