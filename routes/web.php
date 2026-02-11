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


Route::prefix('{locale}')->middleware('set.locale')->group(function () {
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
    });
