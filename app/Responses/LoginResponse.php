<?php

namespace App\Responses;

use Filament\Auth\Http\Responses\LoginResponse as ResponsesLoginResponse;
use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends ResponsesLoginResponse
{

    public function toResponse($request): RedirectResponse|Redirector
    {
        if(auth()->user()->role == 'customer') {
            return redirect()->to(Dashboard::getUrl(panel: 'customer'));
        }

        return parent::toResponse($request);
    }
    
}
