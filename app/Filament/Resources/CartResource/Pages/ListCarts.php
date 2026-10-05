<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use Filament\Resources\Pages\ListRecords;

class ListCarts extends ListRecords
{
    protected static string $resource = CartResource::class;

    // No CreateAction: carts are only ever created by CartController when a
    // customer shops, never manually by an admin — and CartResource doesn't
    // register a 'create' route (see getPages()), so that button has
    // nothing to link to.
}
