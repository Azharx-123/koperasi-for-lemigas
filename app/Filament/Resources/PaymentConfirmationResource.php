<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentConfirmationResource\Pages;
use App\Models\PaymentConfirmation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentConfirmationResource extends Resource
{
    protected static ?string $model = PaymentConfirmation::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Payment Confirmation';

    protected static ?string $pluralModelLabel = 'Payment Confirmations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Submitted At'),
                Tables\Columns\ImageColumn::make('proof_image')
                    ->disk('public')
                    ->height(50),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Not Verified'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('unverified')
                    ->query(fn(Builder $query): Builder => $query->whereNull('verified_at'))
                    ->label('Unverified'),
                Tables\Filters\Filter::make('verified')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('verified_at'))
                    ->label('Verified'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('verify_selected')
                        ->label('Verify Selected')
                        ->action(function ($records) {
                            $errors = [];

                            foreach ($records as $record) {
                                try {
                                    $record->verify();
                                } catch (\RuntimeException $e) {
                                    $errors[] = ($record->order->order_number ?? "#{$record->id}") . ': ' . $e->getMessage();
                                }
                            }

                            if ($errors) {
                                Notification::make()
                                    ->title(count($errors) . ' pembayaran gagal diverifikasi')
                                    ->body(implode("\n", $errors))
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Pembayaran terpilih berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->color('success')
                        ->icon('heroicon-o-check'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentConfirmations::route('/'),
            'create' => Pages\CreatePaymentConfirmation::route('/create'),
            'view' => Pages\ViewPaymentConfirmation::route('/{record}'),
            'edit' => Pages\EditPaymentConfirmation::route('/{record}/edit'),
        ];
    }
}
