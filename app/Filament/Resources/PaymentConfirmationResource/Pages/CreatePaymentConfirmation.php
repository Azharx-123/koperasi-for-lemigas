<?php

namespace App\Filament\Resources\PaymentConfirmationResource\Pages;

use App\Filament\Resources\PaymentConfirmationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentConfirmation extends CreateRecord
{
    protected static string $resource = PaymentConfirmationResource::class;
}
