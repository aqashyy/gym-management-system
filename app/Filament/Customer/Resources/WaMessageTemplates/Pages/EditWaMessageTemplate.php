<?php

namespace App\Filament\Customer\Resources\WaMessageTemplates\Pages;

use App\Filament\Customer\Resources\WaMessageTemplates\WaMessageTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWaMessageTemplate extends EditRecord
{
    protected static string $resource = WaMessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
