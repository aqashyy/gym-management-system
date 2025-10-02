<?php

namespace App\Filament\Resources\FingerDevices\Pages;

use App\Filament\Resources\FingerDevices\FingerDeviceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFingerDevice extends EditRecord
{
    protected static string $resource = FingerDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
