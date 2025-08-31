<?php

namespace App\Filament\Customer\Resources\Members\Schemas;

use App\Services\MemberService;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->default(now())
                    ->required()
                    ->live(onBlur:true),

                Select::make('plan_id')
                    ->label('Plans')
                    ->relationship('Customer.Plans','name')
                    ->searchable()
                    ->preload()
                    ->live(onBlur:true)
                    ->afterStateUpdated(function(Set $set,Get $get, ?string $state) {
                        // dd($state);
                        if($state != null) {
                            $monthsDuration = Filament::auth()->user()->Customer->Plans()->find($state)->duration_months;
                            // $addedDays = 30 * $monthsDuration - 1; //calculating 
                            // set added days to expiry date
                            // dd($get('joining_date'));
                            // $plan_expiry = Carbon::parse($get('joining_date'))->addDays($addedDays);
                            // dd($plan_expiry->format('Y-m-d'));
                            $planExpiry = app(MemberService::class)->calculatePlanExpiry($get('joining_date'),$monthsDuration);

                            $set('plan_expiry', $planExpiry);
                            // dd($addedDays);
                        }
                        // $oneMonth = Carbon::parse($state)->addDays(29);
                        // dd($oneMonth->format('Y-m-d'));
                        // $set('plan_expiry', $oneMonth->format('Y-m-d'));
                    }),

                TextInput::make('photo')
                    ->default(null),
                TextInput::make('fingerprint_id')
                    ->required(),

                Toggle::make('is_staff')
                    ->required(),

                DatePicker::make('plan_expiry'),
            ]);
    }
}
