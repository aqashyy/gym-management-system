<?php

namespace App\Filament\Customer\Resources\Members\Schemas;

use App\Services\MemberService;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
                    ->placeholder('Enter name here')
                    ->required()
                    ->extraInputAttributes([
                        'oninput' => <<<JS
                            // Remove double spaces
                            this.value = this.value.replace(/\\s{2,}/g, ' ');
                            // Capitalize first letter of each word
                            this.value = this.value.replace(/\\b\\w/g, c => c.toUpperCase());

                            // Trim leading spaces
                            this.value = this.value.replace(/^\\s+/, '');
                        JS,
                    ]),

                DatePicker::make('dob')
                    ->required(),

                TextInput::make('phone')
                    ->label('Whatsapp Number')
                    ->placeholder('Enter whatsapp number here')
                    ->prefix('91')
                    ->tel()
                    ->required(),

                TextInput::make('blood_group')
                    ->default(null)
                    ->extraInputAttributes([
                        'style' => 'text-transform: uppercase;',
                        'onkeydown' => <<<JS
                            const allowed = /[A-Za-z+-]/;
                            if (
                                !allowed.test(event.key) && 
                                event.key !== 'Backspace' && 
                                event.key !== 'Delete' && 
                                event.key !== 'ArrowLeft' && 
                                event.key !== 'ArrowRight' && 
                                event.key !== 'Tab'
                            ) {
                                event.preventDefault();
                            }
                        JS,
                    ])
                    ->datalist(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
                    ->placeholder('e.g. A+')
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state)),

                TextInput::make('weight')
                    ->placeholder('Enter weight in Kg')
                    ->postfix('KG')
                    ->numeric()
                    ->default(null),

                TextInput::make('height')
                    ->placeholder('Enter height in CM')
                    ->postfix('CM')
                    ->numeric()
                    ->default(null),

                TextInput::make('fingerprint_id')
                    ->placeholder('Enter member fingerprint ID')
                    ->label('Fingerprint ID (From biometric device)')
                    ->required(),

                Toggle::make('is_staff')
                    ->label('Is Staff .?')
                    ->live(true)
                    ->required(),

                DatePicker::make('joining_date')
                    ->default(now())
                    ->required()
                    ->live(onBlur:true)
                    ->afterStateUpdated(function (Set $set,Get $get, ?string $state) {
                        if($get('plan_id')) {
                            $monthsDuration = Filament::auth()->user()->Customer->Plans()->find($get('plan_id'))->duration_months;
                            $planExpiry = app(MemberService::class)->calculatePlanExpiry($state,$monthsDuration);

                            $set('plan_expiry', $planExpiry);
                        }
                    }),

                Select::make('plan_id')
                    ->label('Plans')
                    ->placeholder('Select plan')
                    ->relationship('Customer.Plans','name')
                    ->searchable()
                    ->preload()
                    ->live(onBlur:true)
                    ->afterStateUpdated(function(Set $set,Get $get, ?string $state) {

                        if($state != null) {
                            $monthsDuration = Filament::auth()->user()->Customer->Plans()->find($state)->duration_months;
                            $planExpiry = app(MemberService::class)->calculatePlanExpiry($get('joining_date'),$monthsDuration);

                            $set('plan_expiry', $planExpiry);
                        }
                    })
                    ->required()
                    ->hidden(fn (Get $get): bool => $get('is_staff')),

                DatePicker::make('plan_expiry')
                    ->label('Plan Expiry (Auto detect as per selected plan & joining date)')
                    ->disabled()
                    ->required(fn (Get $get): bool => $get('is_staff') == true ? false : true)
                    ->hidden(fn (Get $get): bool => $get('is_staff')),

                FileUpload::make('photo')
                    ->imageEditor()
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('450')
                    ->imageResizeTargetHeight('450')
                    ->image(),

            ]);
    }
}
