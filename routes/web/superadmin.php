<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Subscriptions\FeatureController;
use App\Http\Controllers\Subscriptions\PlanController;
use App\Http\Controllers\Subscriptions\SubscriptionController;
use App\Http\Controllers\SuperAdmin\AccessManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use Illuminate\Support\Facades\Route;
/**
 * --------------------------------------------------------------------------
 * SuperAdmin Routes
 * --------------------------------------------------------------------------
 *
 * This file contains all routes related to the "SuperAdmin" section of the system.
 *
 * - All routes are prefixed with the locale and '/superadmin', e.g., /en-SA/superadmin/dashboard
 * - Middleware applied:
 *      - 'web'        => Laravel web middleware group (sessions, CSRF, etc.)
 *      - 'set.locale' => Sets the application locale from the route parameter
 *      - 'role:SuperAdmin' => Ensures only SuperAdmin users can access
 *      - 'permission:roles.manage' => Protects management actions (roles, permissions, etc.)
 * - Route names start with 'customer.superadmin.' (e.g., customer.superadmin.dashboard)
 *
 * This section handles:
 *   - SuperAdmin Dashboard
 *   - Access Management (roles & permissions)
 *   - User Management
 *   - Plans, Features, and Subscriptions management
 *
 * Add all new SuperAdmin-related routes here to keep them organized and maintainable.
 */


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

            Route::post('subscriptions/{subscription}/approve', [SubscriptionController::class, 'approve'])
    ->name('subscriptions.approve');

Route::post('subscriptions/{subscription}/reject', [SubscriptionController::class, 'reject'])
    ->name('subscriptions.reject');


        });




    });




Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware('set.locale')
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
