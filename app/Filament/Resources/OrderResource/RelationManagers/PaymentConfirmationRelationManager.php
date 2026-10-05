<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentConfirmationRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentConfirmation';

    protected static ?string $recordTitleAttribute = 'bank_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('bank_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\DatePicker::make('transfer_date')
                    ->required(),
                Forms\Components\FileUpload::make('proof_image')
                    ->image()
                    ->directory('payment_proofs')
                    ->visibility('public')
                    ->maxSize(8192)
                    ->saveUploadedFileUsing(
                        fn ($file) => \App\Helpers\ImageHelper::optimizeAndStore($file, 'payment_proofs', maxWidth: 1600, maxHeight: 1600, quality: 85)
                    )
                    ->required(),
                Forms\Components\Textarea::make('notes'),
                Forms\Components\Textarea::make('admin_notes'),
                Forms\Components\DateTimePicker::make('verified_at')
                    ->label('Verified At'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bank_name'),
                Tables\Columns\TextColumn::make('account_name'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->date(),
                Tables\Columns\ImageColumn::make('proof_image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Not Verified'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->action(function ($record, Tables\Actions\Action $action) {
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
                    })
                    ->visible(fn($record) => is_null($record->verified_at))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
