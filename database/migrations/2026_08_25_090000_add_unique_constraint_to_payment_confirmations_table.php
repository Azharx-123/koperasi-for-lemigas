<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Before this fix, OrderController::processPaymentConfirmation()
        // didn't re-check payment_status the way showPaymentConfirmation()
        // does, so a double submission (double-click, two open tabs, back
        // button) could create more than one payment_confirmations row for
        // the same order. Collapse any such duplicates before adding the
        // constraint, so this migration doesn't fail on an install where
        // that already happened. Prefer a row an admin already verified
        // (verified_at set) as the keeper; otherwise keep the most recent
        // submission.
        $duplicateOrderIds = DB::table('payment_confirmations')
            ->select('order_id')
            ->groupBy('order_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('order_id');

        foreach ($duplicateOrderIds as $orderId) {
            $rows = DB::table('payment_confirmations')
                ->where('order_id', $orderId)
                ->orderByRaw('verified_at IS NULL') // rows with verified_at set sort first
                ->orderByDesc('created_at')
                ->get();

            $keeper = $rows->first();

            DB::table('payment_confirmations')
                ->where('order_id', $orderId)
                ->where('id', '!=', $keeper->id)
                ->delete();
        }

        Schema::table('payment_confirmations', function (Blueprint $table) {
            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
        });
    }
};
