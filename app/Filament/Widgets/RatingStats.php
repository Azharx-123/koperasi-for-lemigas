<?php

namespace App\Filament\Widgets;

use App\Models\Rating;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RatingStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRatings = Rating::count();
        $totalRatedProducts = Product::whereHas('ratings')->count();
        $averageRating = number_format(Rating::where('is_approved', true)->avg('score') ?: 0, 1);
        $pendingRatings = Rating::where('is_approved', false)->count();

        $topRatedProduct = Product::whereHas('ratings', function ($query) {
            $query->where('is_approved', true);
        })
            ->withCount('ratings')
            ->orderByDesc(
                Rating::selectRaw('AVG(score)')
                    ->whereColumn('product_id', 'products.id')
                    ->where('is_approved', true)
            )
            ->first();

        return [
            Stat::make('Total Ulasan', $totalRatings)
                ->description('Dari ' . $totalRatedProducts . ' produk')
                ->color('primary'),

            Stat::make('Rating Rata-rata', $averageRating . ' dari 5.0')
                ->description('Semua produk')
                ->chart([
                    Rating::where('is_approved', true)->where('score', 5)->count(),
                    Rating::where('is_approved', true)->where('score', 4)->count(),
                    Rating::where('is_approved', true)->where('score', 3)->count(),
                    Rating::where('is_approved', true)->where('score', 2)->count(),
                    Rating::where('is_approved', true)->where('score', 1)->count(),
                ])
                ->color(floatval($averageRating) >= 4 ? 'success' : (floatval($averageRating) >= 3 ? 'warning' : 'danger')),

            Stat::make('Produk Rating Tertinggi', $topRatedProduct ? $topRatedProduct->name : '-')
                ->description($topRatedProduct ? number_format($topRatedProduct->ratings_avg_score, 1) . ' dari 5.0' : 'Tidak ada data')
                ->color('success'),

            Stat::make('Menunggu Approval', $pendingRatings)
                ->description('Perlu dimoderasi')
                ->color($pendingRatings > 0 ? 'warning' : 'success'),
        ];
    }
}
