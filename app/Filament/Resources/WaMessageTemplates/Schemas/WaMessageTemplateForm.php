<?php

namespace App\Filament\Resources\WaMessageTemplates\Schemas;

use App\Models\Customer;
use Filament\Forms\Components\Select;
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
                TextInput::make('title')
                    ->default(null),

                TextInput::make('name')
                    ->required(),

                Select::make('customer_id')
                    ->label('Select customer')
                    ->searchable()
                    ->options(Customer::all()->pluck('name', 'id'))
                    ->required(),

                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                    
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
