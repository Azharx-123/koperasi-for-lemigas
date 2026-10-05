<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Perusahaan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make('Visi, Misi & Sejarah')
                    ->description('Konten ini ditampilkan di halaman About. Kosongkan untuk menyembunyikan bagian terkait.')
                    ->schema([
                        Forms\Components\Textarea::make('vision')
                            ->label('Visi')
                            ->rows(3)
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('mission')
                            ->label('Misi')
                            ->rows(3)
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('history')
                            ->label('Sejarah')
                            ->helperText('Ringkasan singkat; ditampilkan sebelum linimasa sejarah yang sudah ada.')
                            ->rows(5)
                            ->maxLength(65535),
                    ]),
                Forms\Components\Section::make('Rekening Bank')
                    ->description('Ditampilkan sebagai instruksi transfer di halaman checkout.')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_account_number')
                            ->label('Nomor Rekening')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_account_holder')
                            ->label('Atas Nama')
                            ->maxLength(255),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Foto dan Gambar')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('company')
                            ->maxSize(4096)
                            ->saveUploadedFileUsing(
                                fn ($file) => \App\Helpers\ImageHelper::optimizeAndStore($file, 'company', maxWidth: 1000, maxHeight: 1000, quality: 90)
                            ),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('company')
                            ->maxSize(8192)
                            ->saveUploadedFileUsing(
                                fn ($file) => \App\Helpers\ImageHelper::optimizeAndStore($file, 'company', maxWidth: 1920, maxHeight: 1920, quality: 82)
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo')
                    ->square(),
                Tables\Columns\ImageColumn::make('image')
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
