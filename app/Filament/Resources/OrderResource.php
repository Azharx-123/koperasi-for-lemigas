<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::pendingOrderCount();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::pendingOrderCount() > 0 ? 'warning' : 'primary';
    }

    /**
     * Cached within the request so the badge label and its color don't
     * each run their own separate `where('status', 'pending')->count()`
     * query — Filament calls both when rendering the sidebar.
     */
    protected static function pendingOrderCount(): int
    {
        static $count = null;

        return $count ??= static::getModel()::where('status', 'pending')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->helperText('Kosong berarti pembeli sudah menghapus akunnya.'),
                        Forms\Components\Select::make('status')
                            // Only offer statuses this order can legally move to
                            // from its current one (see Order::STATUS_TRANSITIONS),
                            // e.g. a cancelled order can't be reopened here, since
                            // its stock has already been restored elsewhere.
                            ->options(fn (?Order $record) => $record
                                ? collect(Order::STATUSES)->only($record->allowedNextStatuses())->all()
                                : Order::STATUSES)
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->options(Order::PAYMENT_STATUSES)
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->options(Order::PAYMENT_METHODS)
                            ->required(),
                        Forms\Components\TextInput::make('tracking_number')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('shipped_at'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Totals')
                    ->schema([
                        // subtotal/tax/total are recalculated automatically from the
                        // order's items (see Order::recalculateTotals(), triggered by
                        // ItemsRelationManager / OrderItemResource) — leaving them
                        // editable here let an admin's manual correction get silently
                        // overwritten the next time any item changed. Display-only.
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('shipping')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->helperText('Tidak dihitung otomatis dari item — boleh diedit manual.'),
                        Forms\Components\TextInput::make('tax')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('company')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('province')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('district')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postal_code')
                            ->required()
                            ->maxLength(10),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Order Date'),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => Order::STATUS_COLORS[$state] ?? 'secondary')
                    ->formatStateUsing(fn (string $state): string => Order::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => Order::PAYMENT_STATUS_COLORS[$state] ?? 'secondary')
                    ->formatStateUsing(fn (string $state): string => Order::PAYMENT_STATUSES[$state] ?? ucfirst($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Order::STATUSES),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(Order::PAYMENT_STATUSES),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->weight(FontWeight::Bold),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime('d M Y H:i')
                            ->label('Order Date'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Customer'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => Order::STATUS_COLORS[$state] ?? 'secondary')
                            ->formatStateUsing(fn (string $state): string => Order::STATUSES[$state] ?? ucfirst($state)),
                        Infolists\Components\TextEntry::make('payment_status')
                            ->badge()
                            ->color(fn (string $state): string => Order::PAYMENT_STATUS_COLORS[$state] ?? 'secondary')
                            ->formatStateUsing(fn (string $state): string => Order::PAYMENT_STATUSES[$state] ?? ucfirst($state)),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->formatStateUsing(fn (string $state): string => Order::PAYMENT_METHODS[$state] ?? ucfirst($state)),
                        Infolists\Components\TextEntry::make('tracking_number')
                            ->default('Not yet assigned'),
                        Infolists\Components\TextEntry::make('shipped_at')
                            ->dateTime('d M Y H:i')
                            ->default('Not yet shipped'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Totals')
                    ->schema([
                        Infolists\Components\TextEntry::make('formattedSubtotal')
                            ->label('Subtotal'),
                        Infolists\Components\TextEntry::make('formattedShipping')
                            ->label('Shipping'),
                        Infolists\Components\TextEntry::make('formattedTax')
                            ->label('Tax'),
                        Infolists\Components\TextEntry::make('formattedTotal')
                            ->label('Total')
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Customer Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('email'),
                        Infolists\Components\TextEntry::make('phone'),
                        Infolists\Components\TextEntry::make('company')
                            ->default('N/A'),
                        Infolists\Components\TextEntry::make('address')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('province'),
                        Infolists\Components\TextEntry::make('city'),
                        Infolists\Components\TextEntry::make('district'),
                        Infolists\Components\TextEntry::make('postal_code'),
                        Infolists\Components\TextEntry::make('notes')
                            ->default('No notes provided')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\PaymentConfirmationRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
