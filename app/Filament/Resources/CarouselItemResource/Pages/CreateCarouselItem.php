<?php

namespace App\Filament\Resources\CarouselItemResource\Pages;

use App\Filament\Resources\CarouselItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCarouselItem extends CreateRecord
{
    protected static string $resource = CarouselItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
