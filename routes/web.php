<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;

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

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware('set.locale')
    ->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });

Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

// =====================
// Authenticated Shared Routes
// =====================
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['set.locale', 'auth', 'role:Customer|SuperAdmin'])
    ->group(function () {
        // One dashboard route for both roles
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

// =====================
// Modular Route Files
// =====================
require __DIR__ . '/web/customer.php';
require __DIR__ . '/web/superadmin.php';
