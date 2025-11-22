<?php

namespace App\Filament\Resources\CarouselItemResource\Pages;

use App\Filament\Resources\CarouselItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarouselItem extends EditRecord
{
    protected static string $resource = CarouselItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
