<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Order Item';

    protected static ?string $pluralModelLabel = 'Order Items';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->required()
                    ->searchable()
                    // Re-parenting an *existing* item to a different order
                    // from this hidden-from-nav utility resource is an easy
                    // way to accidentally corrupt an order's contents —
                    // only allow choosing it at creation time. (Managing
                    // items in their normal context is what
                    // ItemsRelationManager, nested under the Order page, is
                    // for; this resource exists as a secondary,
                    // cross-order lookup/search view.)
                    ->disabled(fn(string $operation): bool => $operation === 'edit'),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->live(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $set('total', (float) $get('price') * (int) ($get('quantity') ?? 0));
                    })
                    // Deliberately a warning, not a hard block: this resource
                    // exists so an admin CAN correct stock/order data
                    // manually, including edge cases a strict validator would
                    // refuse. The hint below just makes the effect visible
                    // — e.g. entering 999 against a product with 10 in stock
                    // would otherwise silently take it to -989.
                    ->hint(function (callable $get): ?string {
                        $product = \App\Models\Product::find($get('product_id'));
                        if (!$product || !$get('quantity')) {
                            return null;
                        }
                        return $get('quantity') > $product->stock
                            ? "Melebihi stok tersedia ({$product->stock})"
                            : null;
                    })
                    ->hintColor('danger'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $set('total', (float) ($get('price') ?? 0) * (int) $get('quantity'));
                    }),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Product')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where('product_name', 'like', "%{$search}%")
                        ->orWhereHas('product', fn (Builder $q) => $q->where('name', 'like', "%{$search}%")))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('product_name', $direction)),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order')
                    ->relationship('order', 'order_number')
                    ->searchable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'view' => Pages\ViewOrderItem::route('/{record}'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
