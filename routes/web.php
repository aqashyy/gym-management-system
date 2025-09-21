<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return redirect('admin');
});
Route::get('check-expiry/{customerid}/{fingerid}',[ApiController::class, 'checkExpiry']);
// Route::get('customer/login', function () {
//     // return redirect()->route('filament.admin.auth.login');
//     // return "sdgs";
//     Auth::logout();
// });