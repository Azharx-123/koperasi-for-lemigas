<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // OrderController::index() does forUser($id)->orderBy('created_at') —
            // user_id already has an index from its foreign key, but pairing
            // it with created_at avoids a filesort once that index has
            // narrowed the rows down to one user's orders.
            $table->index(['user_id', 'created_at']);

            // status and payment_status are each filtered independently
            // (OrderController::cancel(), and most likely the admin panel's
            // filters/dashboard widgets) rather than always together, so
            // these are separate single-column indexes rather than one
            // composite.
            $table->index('status');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
        });
    }
};
