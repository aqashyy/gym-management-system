<?php

namespace App\Filament\Customer\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('Member.fingerprint_id')
                    ->label('Finger ID')
                    ->icon(Heroicon::OutlinedFingerPrint)
                    ->badge()
                    ->color('info'),
                    
                TextEntry::make('Member.name'),
                
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('recieved_amount')
                    ->numeric(),
                TextEntry::make('paid_on')
                    ->date(),
                TextEntry::make('valid_until')
                    ->date(),
                TextEntry::make('method')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
