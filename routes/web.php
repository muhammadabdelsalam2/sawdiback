<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Subscriptions\FeatureController;
use App\Http\Controllers\Subscriptions\PlanController;
use App\Http\Controllers\Subscriptions\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Redirect / to default locale
Route::get('/', function () {
    return redirect()->route('public.home', ['locale' => config('locale.default', 'en-SA')]);
});

// Public home page
Route::get('{locale}/home', [LandingPageController::class, 'index'])
    ->where('locale', '[a-z]{2}-[A-Z]{2}')
    ->middleware(['set.locale'])
    ->name('public.home');


Route::prefix('{locale}')->middleware('set.locale')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::prefix('{locale?}')
    ->middleware(['set.locale', 'auth']) // set.locale + auth middleware
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::prefix('super-admin')
            // ->middleware('superadmin')
            ->name('superadmin.')
            ->group(function () {
                Route::resource('plans', PlanController::class)->except(['show']);
                Route::get('plans/{plan}/features', [PlanController::class, 'editFeatures'])->name('plans.features.edit');
                Route::put('plans/{plan}/features', [PlanController::class, 'updateFeatures'])->name('plans.features.update');

                Route::resource('features', FeatureController::class)->except(['show']);

                Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
                Route::get('subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
                Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
                Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
                Route::post('subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
                Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');
                Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
                Route::post('subscriptions/{subscription}/expire', [SubscriptionController::class, 'expire'])->name('subscriptions.expire');
            });
    });
