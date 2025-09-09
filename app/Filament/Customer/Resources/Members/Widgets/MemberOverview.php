<?php

namespace App\Filament\Customer\Resources\Members\Widgets;

use App\Models\Member;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MemberOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $customerId = Filament::auth()->user()->Customer->id;

        $totalMembers = Member::count();
        $activeMembers = Member::where('customer_id', $customerId)->where('plan_expiry', '>',now())->count();
        $inactiveMembers = Member::where('customer_id', $customerId)->where('plan_expiry', '<',now())->count();
        return [
            Stat::make('Total Members', $totalMembers)
                ->description('All registered members')
                ->descriptionIcon('heroicon-m-user-group'),

            Stat::make('Active Members', $activeMembers)
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Inactive Members', $inactiveMembers)
                ->description('Currently inactive')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
