<?php

namespace App\Filament\Customer\Resources\Members\Pages;

use App\Filament\Customer\Resources\Members\MemberResource;
use App\Filament\Customer\Resources\Members\Widgets\MemberOverview;
use App\Interfaces\PlanRepoInterface;
use App\Services\MemberService;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Models\Plan;
use App\Models\Payment;
use App\Services\WaMessageService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            MemberOverview::class
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->steps([
                    Step::make('Basic Information')
                    ->schema(self::getBasicInformation())
                    ->columns(2),

                    Step::make('Billing')
                    ->icon(Heroicon::CreditCard)
                    ->schema(self::getBilling())
                    ->hidden(fn (Get $get): bool => $get('is_staff') === true),
            ])
            ->mutateDataUsing(function (array $data) {
                // check is not staff
                if(!$data['is_staff']) {
                    // find selected plan month duration
                    $plan = Plan::find($data['plan_id']);
                    // calculating plan expiry with joining and plan months duration
                    $planExpiry = app(MemberService::class)->calculatePlanExpiry($data['joining_date'],$plan->duration_months);
                    // inserting plan_expiry in array
                    $data['plan_expiry'] = $planExpiry;
                    // set plan amount to array for use after save 
                    $data['plan_amount'] = $plan->price;
                }
                
                // added 91 contry code to phone number
                $data['phone'] = '91'. $data['phone'];
                // insert customer id to array
                $data['customer_id'] = Filament::auth()->user()->Customer->id;
                // dd($data);
                
                return $data;

            })
            ->after(function ($record, $data) {
                // dd($record,$data,'after create');
                // check is not staff
                if(!$data['is_staff']) {
                    Payment::create([
                        'member_id'     => $record->id,
                        'amount'        =>  $data['plan_amount'],
                        'recieved_amount' => $data['recieved_amount'],
                        'method'        =>  $data['payment_method'],
                        'paid_on'       =>  now(),
                        'valid_until'   =>  $data['plan_expiry']
                    ]);
                    
                    // register success message
                    Notification::make()
                        ->title('New member registered Successfully 🎉')
                        ->success()
                        ->body('Member registered successfully. Do you want to send a WhatsApp message?')
                        ->actions([
                            // send subscription message
                            Action::make('send_whatsapp')
                                ->label('Yes, Send')
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->url(function () use ($record, $data) {
                                    // 👇 your WhatsApp sending logic
                                    // $plan = app(PlanRepoInterface::class)->findById($data['plan_id']);
                                    $record->total_amount = $data['plan_amount'];
                                    $record->recieved_amount = $data['recieved_amount'];
                                    $record->balance_amount = $data['plan_amount'] - $data['recieved_amount'];
                                    $record->plan_expiry = $data['plan_expiry'];

                                    $url = app(WaMessageService::class)->getWaMsgLink($record->customer_id, 'payment',$record);
                                    
                                    // open WhatsApp link in new tab
                                    return $url;
                                })
                                ->openUrlInNewTab(),
                            ])
                            ->sendToDatabase(Filament::auth()->user());

                    Notification::make()
                        ->title('Want to send welcome message to '. $record->name)
                        ->success()
                        ->actions([
                            Action::make('send_whatsapp')
                                ->label('Yes, Send')
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->url(function () use ($record, $data) {
                                    // 👇 your WhatsApp sending logic

                                    $url = app(WaMessageService::class)->getWaMsgLink($record->customer_id, 'welcome',$record);
                                    
                                    // open WhatsApp link in new tab
                                    return $url;
                                })
                                ->openUrlInNewTab(),
                        ])
                        ->sendToDatabase(Filament::auth()->user());
                        
                }
            }),
        ];
    }
    private static function getBasicInformation(): array
    {
        return [
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
                    ->options(function () {
                        $plans = Plan::where('customer_id', Filament::auth()->user()->Customer->id )
                        ->get()
                        ->mapWithKeys(function ($plan) {
                            return [ $plan->id => "{$plan->name} - ₹{$plan->price}" ];
                        });

                        return $plans;
                    })
                    ->searchable()
                    ->preload()
                    ->live(onBlur:true)
                    ->afterStateUpdated(function(Set $set,Get $get, ?string $state) {

                        if($state != null) {
                            $plan = Plan::query()
                                ->select('duration_months','price')
                                ->find($state);
                            if($plan) {

                                $monthsDuration = $plan->duration_months;
                                $planExpiry = app(MemberService::class)->calculatePlanExpiry($get('joining_date'),$monthsDuration);
                                $set('total_amount', $plan->price);
                                $set('plan_expiry', $planExpiry);
                                $set('recieved_amount', $plan->price);

                            }

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
                    ->directory('member-photos/'. str_replace(' ','_', Filament::auth()->user()->Customer->name))
                    ->required(),
                ];
    }
    private static function getBilling(): array
    {
        return [
                TextInput::make('total_amount')
                    ->label('Total amount payable')
                    ->prefix('₹')
                    ->readonly()
                    ->required(),
                TextInput::make('recieved_amount')
                    ->label('Recieved amount')
                    ->prefix('₹')
                    ->numeric()
                    ->required()
                    ->afterStateUpdatedJs( <<<'JS'
                            // Set the balance_amount field
                            $set('balance_amount', ( $get('total_amount') - $state));

                    JS),

                TextInput::make('balance_amount')
                    ->prefix('₹')
                    ->readonly(),

                Radio::make('payment_method')
                    ->label('Payment method')
                    ->inline()
                    ->options([
                        'UPI'   => 'UPI (Online)',
                        'CASH'  =>  'CASH (Offline)',
                        'NETBANKING' => 'Netbanking'
                    ])
                    ->required(fn (Get $get) => $get('is_staff') == false)
                    ->hiddenJs(<<<'JS'
                        $get('is_staff')
                    JS),
                ];
    }
}
