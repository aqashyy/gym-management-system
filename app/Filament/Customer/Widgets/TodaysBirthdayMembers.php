<?php

namespace App\Filament\Customer\Widgets;

use App\Filament\Customer\Resources\Members\Tables\MembersTable;
use App\Models\Member;
use App\Services\MemberService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TodaysBirthdayMembers extends TableWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Member::query()
                ->where('customer_id', Filament::auth()->user()->Customer->id)
                ->whereMonth('dob', now()->month)
                ->whereDay('dob', now()->day)
            )
            ->columns([
                Stack::make(MembersTable::getColums())
            ])
            ->emptyStateHeading('No birthdays today')
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
                MembersTable::getWhatsappReminderAction('birthday')
                    ->button(),

                ViewAction::make()->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
