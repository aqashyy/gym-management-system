<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Hash;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->mutateDataUsing(function (array $data) {
                // create user firt with credentials
                $user = User::create([
                    'name' => $data['name'],
                    'email' =>  $data['email'],
                    'password'  =>  Hash::make($data['password'])
                ]);
                
            }),
        ];
    }
}
