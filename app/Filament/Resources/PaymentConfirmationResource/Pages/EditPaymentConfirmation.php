<?php

namespace App\Filament\Resources\PaymentConfirmationResource\Pages;

use App\Filament\Resources\PaymentConfirmationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentConfirmation extends EditRecord
{
    protected static string $resource = PaymentConfirmationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view')
                ->url(fn($record) => PaymentConfirmationResource::getUrl('view', ['record' => $record]))
                ->color('info'),
        ];
    }
}
