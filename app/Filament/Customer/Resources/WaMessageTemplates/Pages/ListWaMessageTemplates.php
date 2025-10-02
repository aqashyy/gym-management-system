<?php

namespace App\Filament\Customer\Resources\WaMessageTemplates\Pages;

use App\Filament\Customer\Resources\WaMessageTemplates\WaMessageTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWaMessageTemplates extends ListRecords
{
    protected static string $resource = WaMessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
