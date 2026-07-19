<?php

use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;

// =====================
// Public Routes
// =====================

// Redirect / to default locale
// Route::get('/', function () {
//     return redirect()->route('public.home', ['locale' => config('locale.default', 'en-SA')]);
// });
Route::get('/', [LandingPageController::class, 'redirectToDefault']);

// Public home page
Route::get('{locale}/home', [LandingPageController::class, 'index'])
    ->where('locale', '[a-z]{2}(?:-[A-Z]{2})?')
    ->middleware(['set.locale'])
    ->name('public.home');

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}(?:-[A-Z]{2})?'])
    ->middleware('set.locale')
    ->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');
        Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
        Route::get('register', [LoginController::class, 'showRegister'])->name('showRegister');
        Route::post('store', [LoginController::class, 'register'])->name('auth.register');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // Social Login Routes
        Route::get('auth/{provider}/redirect', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
        Route::get('auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');
    });
Route::prefix('{locale}')->group(function () {
    Route::get('/terms', [PageController::class, 'terms'])->name('terms.show');
    Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy.show');
});

Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');


// =====================
// Authenticated Shared Routes
// =====================
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}(?:-[A-Z]{2})?'])
    ->middleware(['set.locale', 'auth', 'role:Customer|SuperAdmin'])
    ->group(function () {
        // One dashboard route for both roles
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
