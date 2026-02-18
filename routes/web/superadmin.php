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

        });
    });
