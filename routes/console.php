<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:refresh-product-ratings')->daily();

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

Schedule::command('app:refresh-product-ratings')->daily();

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
