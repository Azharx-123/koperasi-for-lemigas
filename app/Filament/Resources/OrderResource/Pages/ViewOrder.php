<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('Mark as Processing')
                ->color('info')
                ->icon('heroicon-o-cog')
                ->visible(fn($record) => $record->status === 'pending')
                ->action(function ($record) {
                    $record->update(['status' => 'processing']);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('Mark as Shipped')
                ->color('success')
                ->icon('heroicon-o-truck')
                ->form([
                    \Filament\Forms\Components\TextInput::make('tracking_number')
                        ->required(),
                    \Filament\Forms\Components\DateTimePicker::make('shipped_at')
                        ->default(now())
                        ->required(),
                ])
                ->visible(fn($record) => in_array($record->status, ['pending', 'processing']))
                ->action(function ($record, array $data) {
                    $record->update([
                        'status' => 'shipped',
                        'tracking_number' => $data['tracking_number'],
                        'shipped_at' => $data['shipped_at'],
                    ]);
                    $this->refreshFormData(['status', 'tracking_number', 'shipped_at']);
                }),

            Actions\Action::make('Mark as Delivered')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn($record) => $record->status === 'shipped')
                ->action(function ($record) {
                    $record->update(['status' => 'delivered']);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('Mark as Paid')
                ->color('success')
                ->icon('heroicon-o-currency-dollar')
                ->visible(fn($record) => $record->payment_status === 'pending')
                ->action(function ($record) {
                    $record->update(['payment_status' => 'paid']);
                    $this->refreshFormData(['payment_status']);
                }),

            Actions\Action::make('Cancel Order')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn($record) => in_array($record->status, ['pending', 'processing']))
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update(['status' => 'cancelled']);
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
