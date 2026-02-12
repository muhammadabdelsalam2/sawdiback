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
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });

// Switch language
Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');


// =====================
// Protected Routes (auth)
// =====================

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['set.locale', 'auth'])
    ->group(function () {

        // Dashboard for Customer + SuperAdmin
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->middleware('role:Customer|SuperAdmin')
            ->name('dashboard');

        // =========================
        // SuperAdmin Area
        // =========================
        Route::prefix('superadmin')
            ->name('superadmin.')
            ->middleware(['role:SuperAdmin', 'permission:roles.manage'])
            ->group(function () {

                // SuperAdmin dashboard
                Route::get('dashboard', [DashboardController::class, 'superAdminIndex'])
                    ->withoutMiddleware('permission:roles.manage') // لو عايز الداشبورد للـ SuperAdmin حتى بدون permission
                    ->middleware('role:SuperAdmin')
                    ->name('dashboard');

                // Access Management
                Route::get('access-management', [AccessManagementController::class, 'index'])
                    ->name('access-management');

                Route::post('access-management/roles', [AccessManagementController::class, 'storeRole'])
                    ->name('access-management.roles.store');

                Route::post('access-management/permissions', [AccessManagementController::class, 'storePermission'])
                    ->name('access-management.permissions.store');

                Route::put('access-management/roles/{role}/permissions', [AccessManagementController::class, 'updateRolePermissions'])
                    ->name('access-management.roles.permissions.update');

                // Users CRUD
                Route::resource('users', UserManagementController::class)
                    ->except(['show'])
                    ->names('users');

                // =========================
                // Subscriptions / Plans / Features (eslam)
                // =========================
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
