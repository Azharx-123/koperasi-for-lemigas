<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCart extends ViewRecord
{
    protected static string $resource = CartResource::class;

    // No EditAction: same reasoning as ListCarts having no CreateAction —
    // CartResource::getPages() has no 'edit' route, so that button would
    // have nothing to link to either.
}
