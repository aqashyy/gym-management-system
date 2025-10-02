<?php

namespace App\Filament\Resources\FingerDevices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FingerDevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_id')
                    ->label('Customer Name')
                    ->formatStateUsing(fn ($record) => $record->Customer->name),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('ip')
                    ->label('IP Address')
                    ->badge()
                    ->searchable(),

                TextColumn::make('port')
                    ->label('Port Address')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
