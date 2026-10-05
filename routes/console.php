<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:refresh-product-ratings')->daily();
Schedule::command('app:expire-guest-orders')->everyFifteenMinutes();

Artisan::command('app:expire-guest-orders', function () {
    $cutoff = now()->subHour();

    $orders = \App\Models\Order::query()
        ->whereNull('user_id')
        ->where('payment_method', 'bank_transfer')
        ->where('payment_status', 'pending')
        ->where('status', 'pending')
        ->where('created_at', '<=', $cutoff)
        ->with('items')
        ->get();

    $count = 0;

    foreach ($orders as $order) {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                Product::where('id', $item->product_id)->decrement('sold_count', $item->quantity);
            }

            // order_items dan payment_confirmations ikut terhapus lewat
            // foreign key onDelete('cascade') masing-masing (lihat
            // database/migrations untuk kedua tabel itu).
            $order->delete();
        });

        $count++;
    }

    $this->info("Expired {$count} abandoned guest order(s).");

    return static::SUCCESS;
})->purpose('Delete guest checkout orders never paid within an hour of being placed');

Artisan::command('app:refresh-product-ratings', function () {
    $this->info('Starting product ratings refresh...');

    $count = 0;

    Product::chunk(100, function ($products) use (&$count) {
        foreach ($products as $product) {
            $product->refreshRatingCache();
            $count++;
        }
    });

    $this->info("Completed refreshing ratings for {$count} products.");

    return static::SUCCESS;
})->purpose('Refresh the cached ratings for all products');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
