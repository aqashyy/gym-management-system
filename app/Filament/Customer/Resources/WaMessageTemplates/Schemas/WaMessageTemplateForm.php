<?php

namespace App\Filament\Customer\Resources\WaMessageTemplates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WaMessageTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('customer_id')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('name')
                //     ->required(),
                TextInput::make('title')
                    ->disabled()
                    ->default(null)
                    ->belowContent('Keywords: {member_name}, {plan_expiry}, {plan_name}, {gym_name}, {total_amount}, {recieved_amount}, {balance_amount}'),
                
                Textarea::make('content')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
