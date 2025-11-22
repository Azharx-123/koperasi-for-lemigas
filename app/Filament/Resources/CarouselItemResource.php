<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarouselItemResource\Pages;
use App\Models\Carousel_item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CarouselItemResource extends Resource
{
    protected static ?string $model = Carousel_item::class;
    protected static ?string $modelLabel = 'Carousel Items';
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Website Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subtitle')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required()
                    ->imageEditor()
                    ->directory('carousel')
                    ->visibility('public')
                    ->disk('public'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // Menambahkan filter untuk soft deleted items
            ])
            ->actions([
                EditAction::make()
                    ->hidden(fn($record) => $record->trashed()),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Deleted')
                            ->body('Post has been deleted successfully.')
                    ),
                Tables\Actions\ForceDeleteAction::make() // Untuk permanent delete
                    ->requiresConfirmation()
                    ->before(function (Carousel_item $record) {
                        if ($record->image) {
                            Storage::disk('public')->delete($record->image);
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Deleted Permanently')
                            ->body('Post has been permanently deleted.')
                    ),
                Tables\Actions\RestoreAction::make() // Untuk restore soft deleted items
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Restored')
                            ->body('Post has been restored successfully.')
                    ),
            ])
            ->defaultSort('order')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarouselItems::route('/'),
            'create' => Pages\CreateCarouselItem::route('/create'),
            'edit' => Pages\EditCarouselItem::route('/{record}/edit'),
        ];
    }
}
