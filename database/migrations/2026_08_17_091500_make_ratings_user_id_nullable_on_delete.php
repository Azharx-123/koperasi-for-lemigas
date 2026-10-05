<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Changes ratings.user_id's foreign key from cascadeOnDelete() to
     * nullOnDelete(), mirroring
     * 2026_08_16_090000_make_orders_user_id_nullable_on_delete. This is a
     * database-level safety net alongside ProfileController::destroy()'s
     * explicit anonymization: whatever code path deletes a user, their
     * ratings are detached (user_id set to null) rather than destroyed,
     * so reviews survive anonymously and keep contributing to each
     * product's average rating.
     *
     * Uses Schema::table()->change(), which needs no doctrine/dbal on this
     * project's Laravel version — see
     * 2026_08_16_090000_make_orders_user_id_nullable_on_delete for details.
     *
     * Note: ratings has a unique(['user_id', 'product_id']) constraint, but
     * that's unaffected here — MySQL and SQLite both treat each NULL as
     * distinct for uniqueness purposes, so multiple anonymized ratings
     * (even for the same product) can all have a null user_id at once.
     */
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Note: this will fail if any ratings already have a null user_id
        // (i.e. their original reviewer has since deleted their account
        // under the new behavior) — those rows would need to be resolved
        // manually before rolling back.
        Schema::table('ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
