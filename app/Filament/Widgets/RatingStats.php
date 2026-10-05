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
        // One query: counts grouped by approval + score. Everything else
        // below (totals, pending count, average, per-score chart) is
        // derived from this single result set instead of a separate
        // query each.
        $counts = Rating::query()
            ->selectRaw('is_approved, score, COUNT(*) as count')
            ->groupBy('is_approved', 'score')
            ->get();

        $totalRatings = (int) $counts->sum('count');
        $pendingRatings = (int) $counts->where('is_approved', false)->sum('count');

        $approved = $counts->where('is_approved', true);
        $approvedTotal = $approved->sum('count');
        $weightedScoreSum = $approved->sum(fn ($row) => $row->score * $row->count);
        $averageRating = number_format($approvedTotal > 0 ? $weightedScoreSum / $approvedTotal : 0, 1);

        $chart = [];
        foreach ([5, 4, 3, 2, 1] as $score) {
            $chart[] = (int) ($approved->firstWhere('score', $score)->count ?? 0);
        }

        $totalRatedProducts = Rating::query()->distinct('product_id')->count('product_id');

        // ratings_avg_score is computed here (restricted to approved ratings)
        // and reused for both ordering and display, instead of the previous
        // code running an AVG(score) subquery just to order by, then
        // separately reading an "avg" attribute that was never actually
        // selected (it always came back null/0).
        $topRatedProduct = Product::whereHas('ratings', function ($query) {
                $query->where('is_approved', true);
            })
            ->withAvg(['ratings as ratings_avg_score' => function ($query) {
                $query->where('is_approved', true);
            }], 'score')
            ->orderByDesc('ratings_avg_score')
            ->first();

        return [
            Stat::make('Total Ulasan', $totalRatings)
                ->description('Dari ' . $totalRatedProducts . ' produk')
                ->color('primary'),

            Stat::make('Rating Rata-rata', $averageRating . ' dari 5.0')
                ->description('Semua produk')
                ->chart($chart)
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
