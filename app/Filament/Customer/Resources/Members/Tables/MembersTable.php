<?php

namespace App\Filament\Customer\Resources\Members\Tables;

use App\Models\Plan;
use App\Services\MemberService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
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
                ->multiple()
                ->label('Subscription status')
                ->options([
                    '1' => 'Active',
                    '0' =>  'Expired'
                ])->query(function (Builder $query, array $data) {
                    // dd($data);
                    if (! isset($data['status'])) {
                        return $query; // nothing selected → show all
                    }

                    if ($data['status'] == '1') {
                        return $query->where('plan_expiry', '>', now());
                    }

                    if ($data['status'] == '0') {
                        return $query->where('plan_expiry', '<', now());
                    }

                    return $query;
                }),

            ])
            ->recordActions([
                Action::make('sendWhatsapp')
                ->label('')
                ->tooltip('Send Reminder')
                ->icon(Heroicon::OutlinedBellAlert)
                ->url(fn ($record) => self::getWhatsappUrl($record))
                ->openUrlInNewTab()
                ->visible(fn ($record) => app(MemberService::class)->isPlanExpired($record->id) &&  $record->is_staff == 0), // ensures WhatsApp opens

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
    private static function getRenewInformation(): array
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
                ->required(),

                DatePicker::make('new_expiry_date')
                ->disabled(),
            ];
    }
    private static function getBilling(): array
    {
        return [
                TextInput::make('total_amount')
                    ->prefix('₹')
                    ->disabled()
                    ->required(),

                Select::make('payment_method')
                    ->label('Payment method')
                    ->searchable()
                    ->options([
                        'UPI'   => 'UPI (Online)',
                        'CASH'  =>  'CASH (Offline)'
                    ])
                    ->required()
                ];
    }
    protected static function getWhatsappUrl($record): string
    {
        $phone = $record->phone;
        $message = urlencode("Hello {$record->name}, your gym membership expired on {$record->plan_expiry}. Please renew to continue enjoying the services.");

        return "https://wa.me/{$phone}?text={$message}";
    }
    protected static function getColums()
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
                        ->color('primary')
                        ->searchable()
                        ->alignEnd(),
            ]),
            TextColumn::make('phone')
                ->formatStateUsing(fn ($state) => "+".$state)
                ->searchable(),
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
                        return ['expired_date' => $record->plan_expiry];
                    })->steps([

                    Step::make('Renew Information')
                        ->schema(self::getRenewInformation()),
                    Step::make('Billing Information')
                        ->icon(Heroicon::CreditCard)
                        ->schema(self::getBilling())
                    ])
                    ->action(function (array $data, $record) {

                        // dd('hey', $data,$record);
                        $renew_from_date = $data['is_new_renew_date'] == true ? $data['new_renew_date'] : $record->plan_expiry;
                        if($renew_from_date != null) {
                            // renew member plan with renew from date and selected plan
                            $res = app(MemberService::class)
                                    ->renewNow($record, $data['plan_id'], $renew_from_date, $data['payment_method']);

                            if($res == true) {

                                Notification::make('success')
                                ->success()
                                ->body('Member renewed successfullyy')
                                ->send();
                            } else {
                                Notification::make('success')
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
