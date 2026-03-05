<?php

use App\Http\Controllers\Api\Account\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Account\VerifyAccountController;
use App\Http\Controllers\Api\Account\PasswordManagmentController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Guest Auth Routes
    |--------------------------------------------------------------------------
    | Routes for authentication and account verification for guests.
    */
    Route::prefix('auth')
        ->middleware('guest:sanctum')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('api.auth.login');
            Route::post('register', 'register')->name('api.auth.register');
        });

    Route::prefix('account')
        ->middleware(['guest:sanctum', 'throttle:5,1']) // Limit to 5 attempts per minute
        ->group(function () {

            // Account verification
            Route::controller(VerifyAccountController::class)->group(function () {
                Route::post('verifyOtp', 'verifyOtp')->name('api.account.verifyOtp');
                Route::post('resendOtp', 'resendOtp')->name('api.account.resendOtp');
            });

            // Password management
            Route::controller(PasswordManagmentController::class)
                ->prefix('password')
                ->group(function () {
                Route::post('forget', 'forgotPassword')->name('api.password.forget');
                Route::post('forget/verify', 'verifyOtp')->name('api.password.verify');
                Route::post('change', 'resetPassword')->name('api.password.change');
            });

            // Compelete account setup (if needed)
            Route::middleware(['auth:sanctum'])->group(function () {
                Route::post('complete-setup', [AccountController::class, 'complete'])
                    ->name('api.account.completeSetup')->withoutMiddleware('guest:sanctum');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Protected Auth Routes
    |--------------------------------------------------------------------------
    | Routes that require authentication (auth:sanctum)
    */
    Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout')->name('api.auth.logout');
        Route::get('me', 'me')->name('api.auth.me');
    });

});
