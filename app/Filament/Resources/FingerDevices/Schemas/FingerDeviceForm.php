<?php

namespace App\Filament\Resources\FingerDevices\Schemas;

use App\Models\Customer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FingerDeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                    
                Select::make('customer_id')
                    ->label('Select customer')
                    ->searchable()
                    ->options(Customer::all()->pluck('name', 'id'))
                    ->required(),

                TextInput::make('ip')
                    ->label('IP Address')
                    ->required()
                    ->default(null),

                TextInput::make('port')
                    ->label('Port Address')
                    ->required()
                    ->default('4370'),
            ]);
    }
}
