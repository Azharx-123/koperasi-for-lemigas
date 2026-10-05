<?php

namespace App\Filament\Resources\RatingResource\Pages;

use App\Filament\Resources\RatingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRating extends CreateRecord
{
    protected static string $resource = RatingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto verified purchase check
        if (!isset($data['verified_purchase']) || !$data['verified_purchase']) {
            // Check if user has purchased the product
            $verifiedPurchase = \App\Models\Order::where('user_id', $data['user_id'])
                ->whereHas('items', function ($query) use ($data) {
                    $query->where('product_id', $data['product_id']);
                })
                ->where('status', 'delivered')
                ->exists();

            $data['verified_purchase'] = $verifiedPurchase;
        }

        return $data;
    }
}
