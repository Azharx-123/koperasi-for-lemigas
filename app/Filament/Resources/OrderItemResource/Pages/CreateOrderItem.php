<?php

namespace App\Filament\Resources\OrderItemResource\Pages;

use App\Filament\Resources\OrderItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderItem extends CreateRecord
{
    protected static string $resource = OrderItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total'] = $data['price'] * $data['quantity'];
        return $data;
    }
}
