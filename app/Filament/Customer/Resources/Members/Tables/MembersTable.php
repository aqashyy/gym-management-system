<?php

namespace App\Filament\Customer\Resources\Members\Tables;

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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                Action::make('renew_plan')
                ->label('')
                ->icon(Heroicon::ArrowPath)
                ->tooltip('Renew Plan')
                ->fillForm(function ($record): array {
                    return ['expired_date' => $record->plan_expiry];
                })
                ->schema([
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
                                $plan = \App\Models\Plan::query()
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

                            $plan = \App\Models\Plan::query()
                            ->select('duration_months')
                            ->find($get('plan_id'));

                            if($plan) {

                                $planExpiry = app(MemberService::class)->calculatePlanExpiry($state,$plan->duration_months);
                                $set('new_expiry_date', $planExpiry);
                            }
                        }
                    })->live(),

                    Select::make('plan_id')
                    ->label('Plans')
                    ->placeholder('Select plan')
                    ->relationship(name: 'Customer.Plans',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->select('id', 'name', 'price')
                                )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - ₹{$record->price}")
                    // ->searchable()
                    // ->preload()
                    ->live(true)
                    ->afterStateUpdated(function(Set $set,Get $get, ?string $state) {

                        if (! $state) {
                            return;
                        }

                        $renew_from_date = $get('is_new_renew_date') == true
                            ? $get('new_renew_date')
                            : $get('expired_date');

                        $plan = \App\Models\Plan::query()
                            ->select('duration_months')
                            ->find($state);
                        if($plan) {

                            $planExpiry = app(MemberService::class)->calculatePlanExpiry($renew_from_date,$plan->duration_months);

                            $set('new_expiry_date', $planExpiry);
                        }
                    }),

                    Select::make('payment_method')
                    ->label('Payment method')
                    ->searchable()
                    ->options([
                        'UPI'   => 'UPI (Online)',
                        'CASH'  =>  'CASH (Offline)'
                    ])->required(),

                    DatePicker::make('new_expiry_date')
                    ->disabled(),

                ])
                ->action(function (array $data, $record) {
                    dd('hey', $data,$record);
                })->modalSubmitActionLabel('Renew'),

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
