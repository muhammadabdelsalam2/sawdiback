<?php

use App\Http\Controllers\Api\Account\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Account\VerifyAccountController;
use App\Http\Controllers\Api\Account\PasswordManagmentController;

Route::prefix('v1/{locale}')
    ->middleware(['auth:sanctum', 'role:Client'])
    ->group(function () {

        // Categories Routes
        Route::prefix('categories')
            ->name('categories.')
            ->controller(App\Http\Controllers\Api\CategoriesController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        // Account / Profile Routes
        Route::prefix('account')
            ->name('account.')
            ->controller(AccountController::class)
            ->group(function () {
                Route::get('/profile', 'overview')->name('profile.overview');
                Route::patch('/profile', 'updateProfile')->name('profile.update');

                Route::post('/contact/request-update', 'requestContactUpdate')->name('contact.requestUpdate');
                Route::post('/contact/verify-update', 'verifyContactUpdate')->name('contact.verifyUpdate');

                Route::get('/address-book', 'addressBook')->name('address_book.index');
                Route::post('/address-book', 'storeAddress')->name('address_book.store');
                Route::patch('/address-book/{address}', 'updateAddress')->name('address_book.update');
                Route::delete('/address-book/{address}', 'deleteAddress')->name('address_book.delete');

                Route::get('/payment-methods', 'paymentMethods')->name('payment_methods.index');
                Route::post('/payment-methods', 'storePaymentMethod')->name('payment_methods.store');
                Route::delete('/payment-methods/{paymentMethod}', 'deletePaymentMethod')->name('payment_methods.delete');

                Route::get('/notification-settings', 'notificationSettings')->name('notification_settings.show');
                Route::patch('/notification-settings', 'updateNotificationSettings')->name('notification_settings.update');

                Route::post('/logout', 'logout')->name('logout');
                Route::delete('/delete', 'destroy')->name('delete');
            });
    });
