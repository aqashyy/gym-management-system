<?php

namespace App\Filament\Customer\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('member_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('recieved_amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('paid_on')
                    ->required(),
                DatePicker::make('valid_until')
                    ->required(),
                TextInput::make('method')
                    ->default(null),
            ]);
    }
}
