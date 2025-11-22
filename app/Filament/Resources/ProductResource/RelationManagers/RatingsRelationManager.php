<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('score')
                    ->options([
                        1 => '1 Bintang',
                        2 => '2 Bintang',
                        3 => '3 Bintang',
                        4 => '4 Bintang',
                        5 => '5 Bintang',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('review')
                    ->rows(3)
                    ->maxLength(1000),

                Forms\Components\Toggle::make('verified_purchase')
                    ->label('Pembelian Terverifikasi')
                    ->default(false),

                Forms\Components\Toggle::make('is_approved')
                    ->label('Disetujui')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('score')
                    ->label('Rating')
                    ->formatStateUsing(fn(int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color(fn(int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('review')
                    ->label('Ulasan')
                    ->limit(40),

                Tables\Columns\IconColumn::make('verified_purchase')
                    ->label('Terverifikasi')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Disetujui')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        // Refresh product rating cache
                        $this->getOwnerRecord()->refreshRatingCache();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        // Refresh product rating cache
                        $this->getOwnerRecord()->refreshRatingCache();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        // Refresh product rating cache
                        $this->getOwnerRecord()->refreshRatingCache();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Refresh product rating cache
                            $this->getOwnerRecord()->refreshRatingCache();
                        }),

                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['is_approved' => true]));
                            $this->getOwnerRecord()->refreshRatingCache();
                        }),

                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['is_approved' => false]));
                            $this->getOwnerRecord()->refreshRatingCache();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
