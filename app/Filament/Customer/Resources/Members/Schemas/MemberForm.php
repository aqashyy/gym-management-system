<?php

namespace App\Filament\Customer\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('plan_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                DatePicker::make('dob')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('blood_group')
                    ->default(null),
                TextInput::make('weight')
                    ->numeric()
                    ->default(null),
                TextInput::make('height')
                    ->numeric()
                    ->default(null),
                DatePicker::make('joining_date')
                    ->required(),
                TextInput::make('photo')
                    ->default(null),
                TextInput::make('fingerprint_id')
                    ->default(null),
                Toggle::make('is_staff')
                    ->required(),
                DatePicker::make('plan_expiry'),
            ]);
    }
}
