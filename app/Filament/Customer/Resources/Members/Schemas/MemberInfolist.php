<?php

namespace App\Filament\Customer\Resources\Members\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer_id')
                    ->numeric(),
                TextEntry::make('plan_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('dob')
                    ->date(),
                TextEntry::make('phone'),
                TextEntry::make('blood_group'),
                TextEntry::make('weight')
                    ->numeric(),
                TextEntry::make('height')
                    ->numeric(),
                TextEntry::make('joining_date')
                    ->date(),
                TextEntry::make('photo'),
                TextEntry::make('fingerprint_id'),
                IconEntry::make('is_staff')
                    ->boolean(),
                TextEntry::make('plan_expiry')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
