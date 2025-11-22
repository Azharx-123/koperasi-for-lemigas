<?php

namespace App\Filament\Resources\PaymentConfirmationResource\Pages;

use App\Filament\Resources\PaymentConfirmationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentConfirmation extends ViewRecord
{
    protected static string $resource = PaymentConfirmationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('verify')
                ->action(function ($record) {
                    $record->update([
                        'verified_at' => now(),
                    ]);
                    // Also update order payment_status
                    $record->order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);
                    $this->refreshFormData(['verified_at']);
                })
                ->visible(fn($record) => is_null($record->verified_at))
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
        ];
    }
}
