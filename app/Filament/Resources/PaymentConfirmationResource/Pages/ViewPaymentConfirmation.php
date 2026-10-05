<?php

namespace App\Filament\Resources\PaymentConfirmationResource\Pages;

use App\Filament\Resources\PaymentConfirmationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentConfirmation extends ViewRecord
{
    protected static string $resource = PaymentConfirmationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('verify')
                ->action(function ($record, Actions\Action $action) {
                    try {
                        $record->verify();
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Verifikasi gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        $action->halt();
                    }

                    $this->refreshFormData(['verified_at']);
                })
                ->visible(fn($record) => is_null($record->verified_at))
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
        ];
    }
}
