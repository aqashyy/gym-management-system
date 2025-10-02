<?php

namespace App\Filament\Customer\Resources\Members\Tables;

use App\DTOs\RenewDTO;
use App\Interfaces\PlanRepoInterface;
use App\Models\Plan;
use App\Services\MemberService;
use App\Services\WaMessageService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Notifications\Action as NotificationsAction;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make(self::getColums())
            ])
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->recordAction(null)
            ->filters([
                SelectFilter::make('is_staff')
                ->label('Select role')
                ->options([
                    '1' => 'Staff',
                    '0' => 'Member'
                ]),

                SelectFilter::make('plan_expiry')
                ->label('Subscription status')
                ->options([
                    '1' => 'Active',
                    '0' =>  'Expired'
                ])->query(function (Builder $query, array $data) {
                    if ( isset($data['value']) && $data['value'] == null ) {
                        return $query; // nothing selected → show all
                    }
                    // dd($data);

                    if ($data['value'] == '1') {
                        return $query->where('plan_expiry', '>', now());
                    }

                    if ($data['value'] == '0') {
                        // dd('hrlo');
                        return $query->where('plan_expiry', '<', now());
                    }

                    return $query;
                }),
                Filter::make('expiring_in_10_days')
                    ->query(fn ($query) => 
                        $query->whereBetween('plan_expiry', [now(), now()->addDays(10)])
                    ),
                Filter::make('expired_in_30_days')
                    ->query(fn ($query) => 
                        $query->whereBetween('plan_expiry', [now()->subDays(30), now()])
                    ),

            ])
            ->recordActions([

                self::getWhatsappReminderAction('plan_expired')
                    ->visible(fn ($record) => app(MemberService::class)->isPlanExpired($record->id) &&  $record->is_staff == 0),
                self::getRenewAction(),

                ViewAction::make()->label(''),
                EditAction::make()->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRenewSchema(): array
    {
        return [
                DatePicker::make('expired_date')
                        ->disabled(),

                Checkbox::make('is_new_renew_date')
                    ->label('Want to edit renew date..? (else renew from expired date)')
                    ->afterStateUpdated(function (Set $set,Get $get, ?string $state) {
                        if($get('plan_id')) {

                            if($state == true && $get('new_renew_date') != null) {
                                $renew_from_date = $get('new_renew_date');
                            } else {
                                $renew_from_date = $get('expired_date');
                            }
                            $plan = Plan::query()
                                ->select('duration_months')
                                ->find($get('plan_id'));

                            if($plan) {

                                $planExpiry = app(MemberService::class)->calculatePlanExpiry($renew_from_date,$plan->duration_months);
                                $set('new_expiry_date', $planExpiry);
                            }
                        }
                    })->live(),

                DatePicker::make('new_renew_date')
                ->default(now())
                ->label('New renewal date')
                ->visibleJs(fn () => <<<'JS'
                        $get('is_new_renew_date')
                    JS)
                ->afterStateUpdated(function (Set $set,Get $get, ?string $state) {

                    if($get('plan_id')) {

                        $plan = Plan::query()
                        ->select('duration_months')
                        ->find($get('plan_id'));

                        if($plan) {

                            $planExpiry = app(MemberService::class)->calculatePlanExpiry($state,$plan->duration_months);
                            $set('new_expiry_date', $planExpiry);
                        }
                    }
                })
                ->live()
                ->required(fn (Get $get) => $get('is_new_renew_date')),

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

                    if (! $state) {
                        return;
                    }

                    $renew_from_date = $get('is_new_renew_date') == true
                        ? ($get('new_renew_date') ? $get('new_renew_date') : $get('expired_date'))
                        : $get('expired_date');

                    $plan = Plan::query()
                        ->select('duration_months','price')
                        ->find($state);
                    if($plan) {

                        $planExpiry = app(MemberService::class)->calculatePlanExpiry($renew_from_date,$plan->duration_months);

                        $set('total_amount', $plan->price);
                        $set('new_expiry_date', $planExpiry);
                    }
                })
                ->reactive()
                ->required(),

                DatePicker::make('new_expiry_date')
                ->disabled(),
            ];
    }
    public static function getBilling(): array
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
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if(filled($state)) {
                            // Get the value of total_amount
                            $totalAmount = (float) $get('total_amount'); 
                            
                            // Convert the received amount to a float
                            $receivedAmount = (float) $state;

                            // Calculate the balance
                            $balance = $totalAmount - $receivedAmount;

                            // Set the balance_amount field
                            $set('balance_amount', $balance);
                        }
                    }),

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
                    ->required()
                ];
    }
    public static function getWhatsappReminderAction($templateName)
    {
        $label  = 'Send Reminder';
        $icon   =   Heroicon::OutlinedBellAlert;
        $confirmationTitle = 'Send WhatsApp Reminder';
        $confirmationText = 'Are you sure you want to send a WhatsApp reminder to this customer?';

        if($templateName == 'birthday') {
            $label  = 'Send wish';
            $icon   =   Heroicon::Cake; 
            $confirmationTitle = 'Send Birthday Wish';
            $confirmationText = 'Are you sure you want to send a birthday wish to this customer?';
        }
        return Action::make('sendWhatsapp')
                ->label('')
                ->tooltip($label)
                ->icon($icon)
                ->requiresConfirmation()
                ->modalHeading($confirmationTitle)
                ->modalDescription($confirmationText)
                ->modalSubmitActionLabel('Yes, Send')
                ->modalCancelActionLabel('Cancel')
                ->url(function ($record) use($templateName) {
                    
                    return app(WaMessageService::class)
                        ->getWaMsgLink($record->customer_id, $templateName, $record);
                })
                ->openUrlInNewTab(); // ensures WhatsApp opens
    }
    public static function getColums()
    {
        return [
            Split::make([
                ImageColumn::make('photo')
                            ->defaultImageUrl('https://placehold.co/400')
                            ->circular(),
                TextColumn::make('plan_status')
                        ->alignEnd()
                        ->default('Not have plan')
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
            ]),

            Split::make([

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('fingerprint_id')
                        ->label('Finger ID')
                        ->icon(Heroicon::OutlinedFingerPrint)
                        ->badge()
                        ->color('info')
                        ->searchable()
                        ->alignEnd(),
            ]),
            Split::make([

                TextColumn::make('phone')
                    ->formatStateUsing(fn ($state) => "+".$state)
                    ->searchable(),
                TextColumn::make('gender')
                    ->badge()
                    ->alignEnd()
                    ->color(fn ($state) => $state === 'Male' ? 'gray' : Color::Pink),
            ]),
            TextColumn::make('joining_date')
                ->since()
                ->prefix('Joined : ')
                ->sortable(),

            TextColumn::make('plan_expiry')
                ->date()
                ->sortable(),
            ];
    }

    public static function getRenewAction()
    {
        return Action::make('renew_plan')
                    ->label('')
                    ->icon(Heroicon::ArrowPath)
                    ->tooltip('Renew Plan')
                    ->fillForm(function ($record): array {

                        $plan = $plan = Plan::query()
                                            ->select('duration_months','price')
                                            ->find($record->plan_id);
                        return [
                            'expired_date'      => $record->plan_expiry,
                            'plan_id'           => $record->plan_id,
                            'new_expiry_date'   =>  app(MemberService::class)->calculatePlanExpiry($record->plan_expiry, $plan->duration_months),
                            'total_amount'      =>  $plan->price,
                            'recieved_amount'   =>  $plan->price,
                            'balance_amount'    =>  '0 (Full amount recieved)'
                        ];
                    })->steps([

                    Step::make('Renew Information')
                        ->schema(self::getRenewSchema()),

                    Step::make('Billing Information')
                        ->icon(Heroicon::CreditCard)
                        ->schema(self::getBilling()),
                    ])
                    ->action(function (array $data, $record) {

                        // dd('hey', $data,$record);
                        $renew_from_date = $data['is_new_renew_date'] == true ? $data['new_renew_date'] : $record->plan_expiry;
                        if($renew_from_date != null) {
                            // renew member plan with renew from date and selected plan
                            $plan = app(PlanRepoInterface::class)->findById($data['plan_id']);
                            $newExpiry = app(MemberService::class)->calculatePlanExpiry($renew_from_date, $plan->duration_months);
                            // dd($newExpiry);

                            $res = app(MemberService::class)
                                    ->renewNow(RenewDTO::fromArray([
                                        'member_id' =>  $record->id,
                                        'plan_id'   =>  $data['plan_id'],
                                        'renew_from'=>  $renew_from_date,
                                        'payment_method'    =>  $data['payment_method'],
                                        'recieved_amount'   =>  $data['recieved_amount']
                                    ]));

                            if($res == true) {

                                Notification::make('success')
                                    ->title('Renewed Successfully 🎉')
                                    ->success()
                                    ->body('Member renewed successfully. Do you want to send a WhatsApp message?')
                                    ->actions([
                                        
                                        Action::make('send_whatsapp')
                                            ->label('Yes, Send')
                                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                            ->url(function () use ($record, $data, $newExpiry) {
                                                // 👇 your WhatsApp sending logic
                                                $record->total_amount = $data['total_amount'];
                                                $record->recieved_amount = $data['recieved_amount'];
                                                $record->balance_amount = $data['balance_amount'];
                                                $record->plan_expiry = $newExpiry;

                                                $url = app(WaMessageService::class)->getWaMsgLink($record->customer_id, 'payment',$record);
                                                
                                                // open WhatsApp link in new tab
                                                return $url;
                                            })
                                            ->openUrlInNewTab(),
                                    ])
                                    ->sendToDatabase(Filament::auth()->user());

                            } else {
                                Notification::make('error')
                                ->danger()
                                ->body('Something went wrong.. try again..')
                                ->send();
                            }
                        }
                    })
                    ->modalSubmitActionLabel('Renew')
                    ->visible(function ($record)  {
                        if(app(MemberService::class)->isPlanExpired($record->id) && $record->is_staff != 1) {
                            return true;
                        }
                    });
    }
}
