<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Customer\Subscriptions\CustomerSubscriptionController;

Route::prefix('{locale}/customer')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['web', 'set.locale', 'auth', 'role:Customer'])
    ->group(function () {

        Route::get('/ziad', [DashboardController::class, 'index'])
            ->name('customer.dashboard');

        // Customer Subscription
        Route::get('/subscription', [CustomerSubscriptionController::class, 'index'])
            ->name('customer.subscription.index');

        Route::post('/subscription/subscribe', [CustomerSubscriptionController::class, 'subscribe'])
            ->name('customer.subscription.subscribe');

        Route::post('/subscription/change-plan', [CustomerSubscriptionController::class, 'changePlan'])
            ->name('customer.subscription.change-plan');

        Route::post('/subscription/cancel', [CustomerSubscriptionController::class, 'cancel'])
            ->name('customer.subscription.cancel');
    });
