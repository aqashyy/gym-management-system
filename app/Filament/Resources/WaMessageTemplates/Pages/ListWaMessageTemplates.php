<?php

namespace App\Filament\Resources\WaMessageTemplates\Pages;

use App\Filament\Resources\WaMessageTemplates\WaMessageTemplateResource;
use App\Models\Customer;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListWaMessageTemplates extends ListRecords
{
    protected static string $resource = WaMessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add new')
                ->using(function (array $data, string $model): Model {

                    // wa template for create all customers 
                    $customers = Customer::all();
                    $lastCreated = null;

                    foreach($customers as $customer)
                    {
                        $data['customer_id'] = $customer->id;
                        $lastCreated = $model::create($data);
                    }
                    return $lastCreated;

                }),
        ];
    }
}
