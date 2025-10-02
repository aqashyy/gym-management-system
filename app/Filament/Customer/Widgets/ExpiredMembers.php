<?php

namespace App\Filament\Customer\Widgets;

use App\Filament\Customer\Resources\Members\Tables\MembersTable;
use App\Models\Member;
use App\Services\MemberService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpiredMembers extends TableWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Member::query()
            ->where('customer_id', Filament::auth()->user()->Customer->id)
            ->where('plan_expiry', '<', now())
            )
            ->columns([
                Stack::make(MembersTable::getColums())
            ])
            ->emptyStateHeading('No expired members')
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->recordUrl(fn (Member $record): string => route('filament.customer.resources.members.view', $record) . '?redirect=dashboard')

            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                MembersTable::getWhatsappReminderAction('plan_expired')
                    ->visible(fn ($record) => app(MemberService::class)->isPlanExpired($record->id) &&  $record->is_staff == 0),
                MembersTable::getRenewAction(),

                ViewAction::make()->label(''),
                EditAction::make()->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
    
}
