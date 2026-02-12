<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SuperAdmin\AccessManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
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

Route::prefix('{locale}')
    ->middleware('set.locale')
    ->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });

Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['set.locale', 'auth'])
    ->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->middleware('role:Customer|SuperAdmin')
            ->name('dashboard');

        Route::get('superadmin/dashboard', [DashboardController::class, 'superAdminIndex'])
            ->middleware('role:SuperAdmin')
            ->name('superadmin.dashboard');

        Route::get('superadmin/access-management', [AccessManagementController::class, 'index'])
            ->middleware(['role:SuperAdmin', 'permission:roles.manage'])
            ->name('superadmin.access-management');

        Route::post('superadmin/access-management/roles', [AccessManagementController::class, 'storeRole'])
            ->middleware(['role:SuperAdmin', 'permission:roles.manage'])
            ->name('superadmin.access-management.roles.store');

        Route::post('superadmin/access-management/permissions', [AccessManagementController::class, 'storePermission'])
            ->middleware(['role:SuperAdmin', 'permission:roles.manage'])
            ->name('superadmin.access-management.permissions.store');

        Route::put('superadmin/access-management/roles/{role}/permissions', [AccessManagementController::class, 'updateRolePermissions'])
            ->middleware(['role:SuperAdmin', 'permission:roles.manage'])
            ->name('superadmin.access-management.roles.permissions.update');

        Route::resource('superadmin/users', UserManagementController::class)
            ->except(['show'])
            ->middleware(['role:SuperAdmin', 'permission:roles.manage'])
            ->names('superadmin.users');

        // =========================
        // Settings (Super Admin) - MVP (NO resource routes)
        // =========================
        Route::middleware(['role:SuperAdmin', 'permission:roles.manage'])
            ->prefix('settings')
            ->as('settings.')
            ->group(function () {

                // /{locale}/settings => redirect to countries index (MVP)
                Route::get('/', function () {
                    return redirect()->route('settings.countries.index', ['locale' => request()->route('locale')]);
                })->name('index');

                // -------------------------
                // Countries
                // -------------------------
                Route::get('countries', [\App\Http\Controllers\Settings\CountryController::class, 'index'])
                    ->name('countries.index');

                Route::get('countries/create', [\App\Http\Controllers\Settings\CountryController::class, 'create'])
                    ->name('countries.create');

                Route::post('countries', [\App\Http\Controllers\Settings\CountryController::class, 'store'])
                    ->name('countries.store');

                Route::get('countries/{country}/edit', [\App\Http\Controllers\Settings\CountryController::class, 'edit'])
                    ->name('countries.edit');

                Route::put('countries/{country}', [\App\Http\Controllers\Settings\CountryController::class, 'update'])
                    ->name('countries.update');

                Route::delete('countries/{country}', [\App\Http\Controllers\Settings\CountryController::class, 'destroy'])
                    ->name('countries.destroy');

                // -------------------------
                // Cities
                // -------------------------
                Route::get('cities', [\App\Http\Controllers\Settings\CityController::class, 'index'])
                    ->name('cities.index');

                Route::get('cities/create', [\App\Http\Controllers\Settings\CityController::class, 'create'])
                    ->name('cities.create');

                Route::post('cities', [\App\Http\Controllers\Settings\CityController::class, 'store'])
                    ->name('cities.store');

                Route::get('cities/{city}/edit', [\App\Http\Controllers\Settings\CityController::class, 'edit'])
                    ->name('cities.edit');

                Route::put('cities/{city}', [\App\Http\Controllers\Settings\CityController::class, 'update'])
                    ->name('cities.update');

                Route::delete('cities/{city}', [\App\Http\Controllers\Settings\CityController::class, 'destroy'])
                    ->name('cities.destroy');

                // -------------------------
                // Theme (edit/update only)
                // -------------------------
                Route::get('theme', [\App\Http\Controllers\Settings\ThemeController::class, 'edit'])
                    ->name('theme.edit');

                Route::put('theme', [\App\Http\Controllers\Settings\ThemeController::class, 'update'])
                    ->name('theme.update');
            });
    });
