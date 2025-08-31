<?php

namespace App\Filament\Customer\Resources\Plans\Pages;

use App\Filament\Customer\Resources\Plans\PlanResource;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->mutateDataUsing(function (array $data) {
                // dd(Filament::auth()->user()->Customer->Plans);
                $data['customer_id'] = Filament::auth()->user()->Customer->id;
                // dd($data);

                return $data;
            }),
        ];
    }
}
