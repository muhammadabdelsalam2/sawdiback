<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Account\VerifyAccountController;
use App\Http\Controllers\Api\Account\PasswordManagmentController;

Route::prefix('v1')->group(function () {

    // Guest Auth Routes
    Route::prefix('auth')
        ->middleware('guest:sanctum')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('api.auth.login');
            Route::post('register', 'register')->name('api.auth.register');
            // Route::post('verifyOtp', 'verifyOtp')->name('api.auth.verifyOtp');
            Route::post('resendOtp', 'resendOtp')->name('api.auth.resendOtp');
        });
    Route::prefix('account')
        ->middleware('guest:sanctum')
      ->group(function(){
        Route::controller(VerifyAccountController::class)
        ->group(function () {
            Route::post('verifyOtp', 'verifyOtp')->name('api.auth.verifyOtp');
            Route::post('resendOtp', 'resendOtp')->name('api.auth.resendOtp');
        });
        Route::controller(PasswordManagmentController::class)
        ->group(function () {
            Route::prefix('password')->group(function(){
              Route::post('forget', 'forgotPassword')->name('api.password.forget');
            Route::post('change', 'resendOtp')->name('api.password.change'); 
            });
        });
      });

    // Protected Auth Routes
    Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout')->name('api.auth.logout');
        Route::get('me', 'me')->name('api.auth.me');
    });

});