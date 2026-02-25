<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::prefix('v1')->group(function () {

    // Guest Auth Routes
    Route::prefix('auth')
        ->middleware('guest:sanctum')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('api.auth.login');
            Route::post('register', 'register')->name('api.auth.register');
        });

    // Protected Auth Routes
    Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout')->name('api.auth.logout');
        Route::get('me', 'me')->name('api.auth.me');
    });

});