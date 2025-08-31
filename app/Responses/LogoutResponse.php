<?php

namespace App\Responses;

use Filament\Auth\Http\Responses\LogoutResponse as ResponsesLogoutResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse extends ResponsesLogoutResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if(Filament::getCurrentPanel()->getId() === 'customer') {
            return redirect()->to(Filament::getLoginUrl());
        }

        return parent::toResponse($request);
    }
}
