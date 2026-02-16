<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;

use App\Http\Controllers\SuperAdmin\AccessManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;

// Subscriptions 
use App\Http\Controllers\Subscriptions\FeatureController;
use App\Http\Controllers\Subscriptions\PlanController;
use App\Http\Controllers\Subscriptions\SubscriptionController;


// =====================
// Public Routes
// =====================

// Redirect / to default locale
Route::get('/', function () {
    return redirect()->route('public.home', ['locale' => config('locale.default', 'en-SA')]);
});

// Public home page
Route::get('{locale}/home', [LandingPageController::class, 'index'])
    ->where('locale', '[a-z]{2}-[A-Z]{2}')
    ->middleware(['set.locale'])
    ->name('public.home');

// Auth (login/logout) under locale
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware('set.locale')
    ->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');
        Route::post('logout/', [LoginController::class, 'logout'])->name('logout');
    });

// Switch language
Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

