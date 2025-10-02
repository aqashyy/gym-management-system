<?php

namespace App\Filament\Customer\Resources\Payments\Pages;

use App\Filament\Customer\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
