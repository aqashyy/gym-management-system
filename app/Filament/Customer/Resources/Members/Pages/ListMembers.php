<?php

namespace App\Filament\Customer\Resources\Members\Pages;

use App\Filament\Customer\Resources\Members\MemberResource;
use App\Services\MemberService;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->mutateDataUsing(function (array $data) {
                // find selected plan month duration
                $monthsDuration = Filament::auth()->user()->Customer->Plans()->find($data['plan_id'])->duration_months;
                // calculating plan expiry with joining and plan months duration
                $planExpiry = app(MemberService::class)->calculatePlanExpiry($data['joining_date'],$monthsDuration);
                // inserting plan_expiry in array
                $data['plan_expiry'] = $planExpiry;
                // added 91 contry code to phone number
                $data['phone'] = '91'. $data['phone'];
                // insert customer id to array
                $data['customer_id'] = Filament::auth()->user()->Customer->id;
                // dd($data);

                return $data;

            }),
        ];
    }
}
