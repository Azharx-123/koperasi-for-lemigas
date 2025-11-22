<?php

namespace App\Filament\Resources\OrderItemResource\Pages;

use App\Filament\Resources\OrderItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderItem extends EditRecord
{
    protected static string $resource = OrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view')
                ->url(fn($record) => OrderItemResource::getUrl('view', ['record' => $record]))
                ->color('info'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total'] = $data['price'] * $data['quantity'];
        return $data;
    }
}
