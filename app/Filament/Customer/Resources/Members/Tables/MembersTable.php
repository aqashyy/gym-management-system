<?php

namespace App\Filament\Customer\Resources\Members\Tables;

use App\Services\MemberService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fingerprint_id')
                    ->label('Finger ID')
                    ->icon(Heroicon::OutlinedFingerPrint)
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('dob')
                    ->date()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('joining_date')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_staff')
                    ->boolean(),

                TextColumn::make('plan_expiry')
                    ->date()
                    ->sortable(),

                TextColumn::make('plan_status')
                    ->default('Not have plan')
                    ->formatStateUsing(function ($record) {
                        // return $record->id;
                        if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                            return 'Expired';
                        }
                        return 'Active';
                    })
                    ->icon(function ($record) {
                        if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                            return Heroicon::XCircle;
                        }
                        return Heroicon::CheckCircle;
                    })
                    ->iconColor(function ($record) {
                        if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                            return 'danger';
                        }
                        return 'success';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                            return 'danger';
                        }
                        return 'success';
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(''),
                EditAction::make()->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
