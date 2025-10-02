<?php

namespace App\Filament\Customer\Resources\FingerDevices\Schemas;

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
                TextInput::make('customer_id')
                    ->visible(false)
                    ->required()
                    ->numeric(),
                TextInput::make('ip')
                    ->label('IP Address')
                    ->default(null),

                TextInput::make('port')
                    ->label('Port Address')
                    ->required()
                    ->default('4370'),
            ]);
    }
}
