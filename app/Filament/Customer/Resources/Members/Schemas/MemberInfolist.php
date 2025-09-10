<?php

namespace App\Filament\Customer\Resources\Members\Schemas;

use App\Filament\Customer\Resources\Members\Tables\MembersTable;
use App\Interfaces\PlanRepoInterface;
use App\Services\MemberService;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                        ImageEntry::make('photo')
                        ->hiddenLabel()
                        ->alignCenter()
                        ->circular()
                        ->imageWidth(150)
                        ->imageHeight(150)
                        ->defaultImageUrl('https://placehold.co/400'),

                        Section::make([
                            Grid::make()
                                ->schema([
                                    
                                    TextEntry::make('fingerprint_id')
                                        ->hiddenLabel()
                                        ->icon(Heroicon::OutlinedFingerPrint)
                                        ->badge()
                                        ->color('success'),

                                    TextEntry::make('name')
                                        ->hiddenLabel()
                                        ->weight('bold')
                                        ->size('xl'),
                                ])
                                ->columns(['default' => 2, 'md' => 2]),

                            TextEntry::make('phone')
                                ->hiddenLabel()
                                ->icon('heroicon-o-phone')
                                ->copyable()
                                ->formatStateUsing(fn ($state) => "+".$state)
                                ->copyMessage('Phone copied!'),

                            Grid::make()
                            ->schema([
                                TextEntry::make('plan_expiry')
                                        ->hiddenLabel()
                                        ->formatStateUsing(function ($record) {
                                            // return $record->id;
                                            if($record->is_staff === 1) {
                                                return 'Staff';
                                            }
                                            if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                                                return 'Expired';
                                            }
                                            return 'Active';
                                        })
                                        ->icon(function ($record) {
                                            if($record->is_staff === 1) {
                                                return Heroicon::CheckCircle;
                                            }
                                            if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                                                return Heroicon::XCircle;
                                            }
                                            return Heroicon::CheckCircle;
                                        })
                                        ->iconColor(function ($record) {
                                            if($record->is_staff === 1) {
                                                return 'primary';
                                            }
                                            if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                                                return 'danger';
                                            }
                                            return 'success';
                                        })
                                        ->badge()
                                        ->color(function ($record) {
                                            if($record->is_staff === 1) {
                                                return 'primary';
                                            }
                                            if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                                                return 'danger';
                                            }
                                            return 'success';
                                }),
                                TextEntry::make('gender')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->color(fn ($state) => $state === 'Male' ? 'primary' : 'pink'),


                            ])
                            ->columns(['default' => 2 , 'md' => 2])
                            
                        ])
                        ->columns(1),
                        // extra info section
                        Section::make([
                            TextEntry::make('dob')
                                ->icon(Heroicon::Cake)
                                ->label('DOB')
                                ->hiddenLabel()
                                ->date(),

                            TextEntry::make('blood_group')
                                ->label('Blood Group')
                                ->icon(Heroicon::Heart)
                                ->hiddenLabel(),

                            TextEntry::make('weight')
                                ->numeric()
                                ->suffix(' kg')
                                ->hiddenLabel(),
                            TextEntry::make('height')
                                ->numeric()
                                ->suffix(' cm')
                                ->hiddenLabel(),
                        ])
                        ->columns(['default' => 2, 'md' => 2])
                        ->columnSpanFull(),
                ])
                ->columns([
                    'default' => 1, // mobile → stack (image on top, details below)
                    'md' => 2,      // desktop → 2 columns (image left, details right)
                ]),




                TextEntry::make('plan_id')
                    ->label('Current plan')
                    ->formatStateUsing(fn ($state) => app(PlanRepoInterface::class)->findById($state)->name),
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
                ->afterLabel(MembersTable::getRenewAction())

                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
