<?php

use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Api\Account\PasswordManagmentController;
use App\Http\Controllers\Api\Account\VerifyAccountController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Api\Product\ProductController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/ecommerce.php';

Route::prefix('v1/{locale}')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Product Routes (Mobile App)
    |--------------------------------------------------------------------------
    */
    Route::get('products/best-selling', [ProductController::class, 'bestSelling'])
        ->name('products.best-selling');

    /*
    |--------------------------------------------------------------------------
    | Guest Auth Routes
    |--------------------------------------------------------------------------
    | Routes for authentication and account verification for guests.
    */
    Route::prefix('auth')
        ->middleware(['guest:sanctum', 'throttle:10,1', 'api.error'])
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('api.auth.login');

            Route::post('social-login', 'socialLogin')->name('api.auth.social-login');
            Route::post('register', 'register')->name('api.auth.register');

            Route::prefix('social')->group(function () {
                Route::get('{provider}/redirect', [SocialAuthController::class, 'redirect']);
                Route::get('{provider}/callback', [SocialAuthController::class, 'callback']);
            });
        });

    Route::prefix('account')
        ->middleware(['guest:sanctum', 'throttle:5,1'])
        ->group(function () {

            Route::controller(VerifyAccountController::class)->group(function () {
                Route::post('verifyOtp', 'verifyOtp')->name('api.account.verifyOtp');
                Route::post('resendOtp', 'resendOtp')->name('api.account.resendOtp');
            });

            Route::controller(PasswordManagmentController::class)
                ->prefix('password')
                ->group(function () {
                    Route::post('forget', 'forgotPassword')->name('api.password.forget');
                    Route::post('forget/verify', 'verifyOtp')->name('api.password.verify');
                    Route::post('change', 'resetPassword')->name('api.password.change');
                });

            Route::middleware(['auth:sanctum'])->group(function () {
                Route::post('complete-setup', [AccountController::class, 'complete'])
                    ->name('api.account.completeSetup')
                    ->withoutMiddleware('guest:sanctum');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Protected Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout')->name('api.auth.logout');
        Route::post('social-link', 'socialLink')->name('api.auth.social-link');
        Route::get('me', 'me')->name('api.auth.me');
    });

});