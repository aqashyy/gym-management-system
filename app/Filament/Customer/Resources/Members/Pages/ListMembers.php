<?php

namespace App\Filament\Customer\Resources\Members\Pages;

use App\Filament\Customer\Resources\Members\MemberResource;
use App\Services\MemberService;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Models\Plan;
use App\Models\Payment;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->mutateDataUsing(function (array $data) {
                // find selected plan month duration
                $plan = Plan::find($data['plan_id']);
                // calculating plan expiry with joining and plan months duration
                $planExpiry = app(MemberService::class)->calculatePlanExpiry($data['joining_date'],$plan->duration_months);
                // inserting plan_expiry in array
                $data['plan_expiry'] = $planExpiry;
                // added 91 contry code to phone number
                $data['phone'] = '91'. $data['phone'];
                // insert customer id to array
                $data['customer_id'] = Filament::auth()->user()->Customer->id;
                // dd($data);
                // set plan amount to array for use after save 
                $data['plan_amount'] = $plan->price;

                return $data;

            })
            ->after(function ($record, $data) {
                // dd($record,$data,'after create');
                Payment::create([
                    'member_id'     => $record->id,
                    'amount'        =>  $data['plan_amount'],
                    'method'        =>  $data['payment_method'],
                    'paid_on'       =>  now(),
                    'valid_until'   =>  $data['plan_expiry']
                ]);
            }),
        ];
    }
}
