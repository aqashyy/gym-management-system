<?php

namespace App\Filament\Customer\Resources\Members\Schemas;

use App\Filament\Customer\Resources\Members\Tables\MembersTable;
use App\Interfaces\PlanRepoInterface;
use App\Models\Balance;
use App\Models\Payment;
use App\Services\MemberService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Profile section
                Section::make([
                        ImageEntry::make('photo')
                        ->hiddenLabel()
                        ->alignCenter()
                        ->circular()
                        ->imageWidth(150)
                        ->imageHeight(150)
                        ->defaultImageUrl('https://placehold.co/400'),
                        // personal info
                        Section::make([
                            Grid::make()
                                ->schema([

                                    TextEntry::make('fingerprint_id')
                                        ->hiddenLabel()
                                        ->icon(Heroicon::OutlinedFingerPrint)
                                        ->badge()
                                        ->color('info'),

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
                                        ->default('Staff')
                                        ->formatStateUsing(function ($record) {
                                            // return $record->id;
                                            if($record->is_staff == 1) {
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
                                    ->color(fn ($state) => $state === 'Male' ? 'gray' : Color::Pink),


                            ])
                            ->columns(['default' => 2 , 'md' => 2])

                        ])
                        ->columns(1),
                        // health info section
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
                                ->icon(Heroicon::Scale)
                                ->suffix(' kg')
                                ->hiddenLabel(),
                            TextEntry::make('height')
                                ->numeric()
                                ->icon(Heroicon::ChevronDoubleUp)
                                ->suffix(' cm')
                                ->hiddenLabel(),
                        ])
                        ->columns(['default' => 2, 'md' => 2])
                        ->columnSpanFull(),
                ])
                ->columnSpan(fn ($record) => $record->is_staff ? 'full' : 'mid' )
                ->columns([
                    'default' => 1, // mobile → stack (image on top, details below)
                    'md' => 2,      // desktop → 2 columns (image left, details right)
                ]),
                // Membership details section
                Section::make('Memberships')
                        ->schema([
                            // plan status group
                            Grid::make()
                                ->schema([
                                    TextEntry::make('plan_id')
                                        ->hiddenLabel()
                                        ->alignLeft()
                                        ->formatStateUsing(fn ($state) => app(PlanRepoInterface::class)->findById($state)->name),
                                    TextEntry::make('plan_expiry')
                                        ->hiddenLabel()
                                        ->formatStateUsing(function ($record) {
                                            // return $record->id;
                                            if($record->is_staff === 1) {
                                                return 'Staff';
                                            }
                                            if ( app(MemberService::class)->isPlanExpired($record->id) ) {
                                                return 'Expired ' . Carbon::parse($record->plan_expiry)->diffForHumans(now(), true). ' ago';
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
                                ])
                                ->columns(['default' => 2, 'md' => 2]),
                            // dates group
                            Grid::make()
                                ->schema([
                                    TextEntry::make('joining_date')
                                        ->label('Joined Date')
                                        ->date(),
                                    TextEntry::make('plan_expiry')
                                        ->label('Expire Date')
                                        ->date(),
                                ])
                                ->columns(['default' => 2, 'md' => 2]),
                                // actions group
                            Grid::make()
                                ->schema([
                                    MembersTable::getRenewAction()->label('Renew Now')
                                        ->size(Size::Large),

                                    MembersTable::getWhatsappReminderAction('renew')
                                        ->label('Send Reminder')
                                        ->visible(fn ($record) => app(MemberService::class)->isPlanExpired($record->id) &&  $record->is_staff == 0),
                                ])
                                ->columns(['default' => 2, 'md' => 2]),
                        ])
                        ->visible(fn ($record) => !$record->is_staff),
                // Balance info
                Section::make('Balance')
                    ->icon(Heroicon::CurrencyRupee)
                    ->schema([  
                        TextEntry::make('name')
                            ->hiddenLabel()
                            ->alignCenter()
                            ->badge()
                            ->color(function ($record) {
                                    $balance = $record->amount - $record->recieved_amount;
                                    if($balance == 0) {
                                        return "info";
                                    }
                                    return "warning";
                            })
                            ->formatStateUsing(function ($record) {
                                $payments = $record->Payments;
                                $balance = 0;
                                
                                foreach ($payments as $payment) {
                                    if($payment->amount != $payment->recieved_amount) {

                                        $balance += $payment->amount - $payment->recieved_amount;

                                    }
                                }
                                if($balance == 0) {
                                    return "No have balance";
                                }
                                return "Total ₹".$balance;
                            }),
                        RepeatableEntry::make('Payments')
                            ->schema([
                                Grid::make()
                                ->schema([
                                    TextEntry::make('amount')
                                        ->label('Total amount'),
                                    TextEntry::make('amount')
                                        ->hiddenLabel()
                                        ->badge()
                                        // ->afterLabel(function ($record) {
                                        //     return Action::make('pay_balance');
                                        // })
                                        ->color(function ($record) {
                                            $balance = $record->amount - $record->recieved_amount;
                                            if($balance == 0) {
                                                return "success";
                                            }
                                            return "warning";
                                        })
                                        ->formatStateUsing(function ($record) {
                                            $balance = $record->amount - $record->recieved_amount;
                                            if($balance == 0) {
                                                return "Fully paid";
                                            }
                                            return "Balance ₹".$balance;
                                        }),
                                        
                                ])
                                ->columns(['default' => 2, 'md' => 2]),
                                Grid::make()
                                        ->schema([

                                            TextEntry::make('paid_on'),

                                            TextEntry::make('amount')
                                                ->hiddenLabel()
                                                ->belowContent(function ($record) {
                                                    return self::payBalanceAction($record);
                                                })
                                                ->formatStateUsing(fn ($record) => ''),

                                        ])->columns(['default' => 2, 'md' => 2]),

                                TextEntry::make('recieved_amount')
                            ])
                            // ->url('/customer/payments')
                    ])
                    ->visible(fn ($record) => !$record->is_staff),

            ]);
    }

    protected static function payBalanceAction($record)
    {
        return Action::make('pay_balance')
                    ->icon(Heroicon::CurrencyDollar)
                    ->button()
                    ->fillForm(function () use($record) {
                        // dd($record);
                        return [
                            'balance_amount' => $record->amount - $record->recieved_amount,
                            'paying'         => 1,
                        ];
                    })
                    ->schema([
                        TextInput::make('balance_amount')
                            ->prefix('₹')
                            ->readOnly(),
                        Radio::make('paying')
                            ->options([
                                1 => 'Full amount',
                                0 => 'Custom'
                            ])
                            ->live()
                            ->inline()
                            ->required(),

                        TextInput::make('recieved_amount')
                            ->visibleJs(<<<'JS'
                                $get('paying') == 0
                            JS)
                            ->numeric()
                            ->rules(['integer', 'min:0', 'max:'. $record->amount])
                            ->maxLength(fn (Get $get) => strlen($get('balance_amount')))
                            ->required(fn (Get $get) => $get('paying') == 0)

                    ])->action(function ($data) use ($record){

                        if($data['paying'] == 0) {
                            $payed_amount   =   $data['recieved_amount'];
                        } else {
                            $payed_amount   =   $data['balance_amount'];
                        }
                        // dd($record);
                        // update balance amount of payments
                        $record->recieved_amount += $payed_amount;
                        $record->save();

                        // insert balance amount to balances
                        $balances   =   new Balance();
                        $balances->fill([
                            'member_id'     => $record->member_id,
                            'payment_id'    =>  $record->id,
                            'amount'        =>  $payed_amount
                        ]);
                        $balances->save();

                        Notification::make('success')
                            ->success()
                            ->body("Balance amount {$payed_amount} successfull payed")
                            ->send();
                        
                    })
                    ->visible(fn () => $record->amount != $record->recieved_amount)
                    ->modalSubmitActionLabel('Confirm');
    }
}
