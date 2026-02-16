<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Subscriptions\FeatureController;
use App\Http\Controllers\Subscriptions\PlanController;
use App\Http\Controllers\Subscriptions\SubscriptionController;
use App\Http\Controllers\Livestock\LivestockAnimalController;
use App\Http\Controllers\Livestock\LivestockOperationsController;
use App\Http\Controllers\Livestock\AnimalSpeciesController;
use App\Http\Controllers\Livestock\AnimalBreedController;
use App\Http\Controllers\Livestock\FeedTypeController;
use App\Http\Controllers\Livestock\VaccineController;
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

            // =========================
            // Livestock Management
            // =========================
            Route::prefix('livestock')->name('livestock.')->group(function () {
                Route::resource('species', AnimalSpeciesController::class)->except(['show']);
                Route::resource('breeds', AnimalBreedController::class)->except(['show']);
                Route::resource('feed-types', FeedTypeController::class)->except(['show']);
                Route::resource('vaccines', VaccineController::class)->except(['show']);

                Route::get('animals', [LivestockAnimalController::class, 'index'])->name('animals.index');
                Route::get('animals/create', [LivestockAnimalController::class, 'create'])->name('animals.create');
                Route::post('animals', [LivestockAnimalController::class, 'store'])->name('animals.store');
                Route::get('animals/{animal}', [LivestockAnimalController::class, 'show'])->name('animals.show');
                Route::get('animals/{animal}/edit', [LivestockAnimalController::class, 'edit'])->name('animals.edit');
                Route::put('animals/{animal}', [LivestockAnimalController::class, 'update'])->name('animals.update');
                Route::post('animals/{animal}/status', [LivestockAnimalController::class, 'changeStatus'])->name('animals.status.change');

                Route::post('feeding-logs', [LivestockOperationsController::class, 'recordFeeding'])->name('feeding-logs.store');
                Route::post('milk-production-logs', [LivestockOperationsController::class, 'recordMilkProduction'])->name('milk-production-logs.store');
                Route::post('health-records', [LivestockOperationsController::class, 'recordHealth'])->name('health-records.store');
                Route::post('vaccinations', [LivestockOperationsController::class, 'recordVaccination'])->name('vaccinations.store');
                Route::post('weight-logs', [LivestockOperationsController::class, 'recordWeight'])->name('weight-logs.store');

                Route::get('reproduction-cycles', [LivestockOperationsController::class, 'listCycles'])->name('reproduction-cycles.index');
                Route::post('reproduction-cycles', [LivestockOperationsController::class, 'openReproductionCycle'])->name('reproduction-cycles.store');
                Route::post('reproduction-cycles/{cycle}/insemination', [LivestockOperationsController::class, 'inseminateCycle'])->name('reproduction-cycles.insemination');
                Route::post('reproduction-cycles/{cycle}/pregnancy-check', [LivestockOperationsController::class, 'pregnancyCheckCycle'])->name('reproduction-cycles.pregnancy-check');
                Route::post('reproduction-cycles/{cycle}/birth', [LivestockOperationsController::class, 'recordBirth'])->name('reproduction-cycles.birth');

                Route::get('alerts/vaccinations-due', [LivestockOperationsController::class, 'vaccinationDueAlerts'])->name('alerts.vaccinations-due');
                Route::get('alerts/vaccinations-overdue', [LivestockOperationsController::class, 'vaccinationOverdueAlerts'])->name('alerts.vaccinations-overdue');
                Route::get('alerts/expected-deliveries', [LivestockOperationsController::class, 'expectedDeliveries'])->name('alerts.expected-deliveries');
                Route::get('alerts/under-treatment', [LivestockOperationsController::class, 'underTreatmentAnimals'])->name('alerts.under-treatment');
            });
        });
    });
