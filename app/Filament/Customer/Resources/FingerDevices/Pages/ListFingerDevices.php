<?php

namespace App\Filament\Customer\Resources\FingerDevices\Pages;

use App\Filament\Customer\Resources\FingerDevices\FingerDeviceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFingerDevices extends ListRecords
{
    protected static string $resource = FingerDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
