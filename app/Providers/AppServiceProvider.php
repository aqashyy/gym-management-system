<?php

namespace App\Providers;

use App\Interfaces\MemberRepoInterface;
use App\Repositories\MemberRepo;
use App\Responses\LoginResponse;
use App\Responses\LogoutResponse;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as FilamentLoginResponse;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as FilamentLogoutResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        FilamentLoginResponse::class => LoginResponse::class,
        FilamentLogoutResponse::class => LogoutResponse::class
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MemberRepoInterface::class, MemberRepo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
