<?php

namespace App\Filament\Customer\Resources\Members\Pages;

use App\Filament\Customer\Resources\Members\MemberResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(function () {
                    $redirect = request()->query('redirect');

                    return match ($redirect) {
                        'dashboard' => route('filament.customer.pages.dashboard'),
                        default     => static::getResource()::getUrl('index'),
                    };
                })
                ->color('danger'),
            EditAction::make(),
        ];
    }
}
