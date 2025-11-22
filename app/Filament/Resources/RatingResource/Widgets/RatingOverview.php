<?php

namespace App\Filament\Resources\RatingResource\Widgets;

use App\Models\Rating;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RatingOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRatings = Rating::count();
        $pendingApproval = Rating::where('is_approved', false)->count();
        $averageRating = number_format(Rating::where('is_approved', true)->avg('score') ?: 0, 1);

        // Distribusi rating
        $distribution = Rating::where('is_approved', true)
            ->selectRaw('score, count(*) as count')
            ->groupBy('score')
            ->orderBy('score', 'desc')
            ->pluck('count', 'score')
            ->toArray();

        $fiveStars = $distribution[5] ?? 0;
        $fourStars = $distribution[4] ?? 0;
        $threeStars = $distribution[3] ?? 0;
        $twoStars = $distribution[2] ?? 0;
        $oneStar = $distribution[1] ?? 0;

        $highRatings = $fiveStars + $fourStars;
        $lowRatings = $oneStar + $twoStars;

        $highRatingsPercent = $totalRatings > 0 ? round(($highRatings / $totalRatings) * 100) : 0;

        return [
            Stat::make('Total Ulasan', $totalRatings)
                ->description('Seluruh ulasan yang diberikan')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->chart([
                    $fiveStars,
                    $fourStars,
                    $threeStars,
                    $twoStars,
                    $oneStar
                ])
                ->color('gray'),

            Stat::make('Rating Rata-rata', $averageRating)
                ->description($highRatingsPercent . '% ulasan positif')
                ->descriptionIcon('heroicon-m-star')
                ->color($averageRating >= 4 ? 'success' : ($averageRating >= 3 ? 'warning' : 'danger')),

            Stat::make('Menunggu Persetujuan', $pendingApproval)
                ->description('Ulasan yang perlu dimoderasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingApproval > 0 ? 'warning' : 'success'),
        ];
    }
}
