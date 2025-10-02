<?php

namespace App\Filament\Customer\Widgets;

use App\Filament\Customer\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerDashboardStat extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $customerId = Filament::auth()->user()->Customer->id;
        return [
            Stat::make('', Member::where('customer_id', $customerId)
                            ->where('plan_expiry', '>', now())->count()
                            )
                ->descriptionIcon('heroicon-m-user-group')
                ->description('Active Members')
                ->color(Color::Blue)
                ->columns(['default' => 2, 'md' => 2]),

            Stat::make('', Member::where('customer_id', $customerId)
                            ->whereBetween('plan_expiry', [now()->subDays(30), now()])
                            ->count())
                ->url(
                    MemberResource::getUrl('index', [
                        'filters[expired_in_30_days][isActive]=' => true,
                    ]))
                ->description('Expired in 30 days')
                ->descriptionIcon('heroicon-m-user-group')
                ->color(Color::Red)
                ->columns(['default' => 2, 'md' => 2]),

            Stat::make('', Member::where('customer_id', $customerId)
                            ->whereBetween('plan_expiry', [now(), now()->addDays(10)])
                            ->count())
                ->url(
                    MemberResource::getUrl('index', [
                        'filters[expiring_in_10_days][isActive]=' => true,
                    ]))
                ->description('Expiring in 10 days')
                ->descriptionIcon('heroicon-m-user-group')
                ->color(Color::Yellow)
                ->columns(['default' => 2, 'md' => 2]),

            Stat::make('', Member::where('customer_id', $customerId)
                            ->where('is_staff', 0)->count())
                ->description('Total Members')
                ->descriptionIcon('heroicon-m-user-group')
                ->columns(['default' => 2, 'md' => 2]),
        ];
    }
}
