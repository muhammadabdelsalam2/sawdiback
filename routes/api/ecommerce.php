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


    });
