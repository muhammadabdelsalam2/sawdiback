<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\CityController;
use App\Http\Controllers\Settings\CountryController;
use App\Http\Controllers\Settings\ThemeController;
use App\Http\Controllers\Subscriptions\FeatureController;
use App\Http\Controllers\Subscriptions\PlanController;
use App\Http\Controllers\Subscriptions\SubscriptionController;
use App\Http\Controllers\SuperAdmin\AccessManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['set.locale', 'auth', 'role:SuperAdmin'])
    ->name('superadmin.')
    ->group(function () {

        // SuperAdmin dashboard (no permission required)
        Route::get('superadmin/dashboard', [DashboardController::class, 'superAdminIndex'])
            ->name('dashboard');

        // Anything below requires roles.manage permission
        Route::middleware(['permission:roles.manage'])
            ->prefix('superadmin')
            ->group(function () {

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

            // Plans / Features
            Route::resource('plans', PlanController::class)->except(['show']);
            Route::get('plans/{plan}/features', [PlanController::class, 'editFeatures'])->name('plans.features.edit');
            Route::put('plans/{plan}/features', [PlanController::class, 'updateFeatures'])->name('plans.features.update');

            Route::resource('features', FeatureController::class)->except(['show']);

            // Subscriptions
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

        Route::prefix('setting')
            ->name('setting.')
            ->group(function () {
                Route::prefix('countries')
                    ->name('countries.')
                    ->group(function () {
                        Route::get('/', [CountryController::class, 'index'])->name('index');
                        Route::get('/create', [CountryController::class, 'create'])->name('create');
                        Route::post('/store', [CountryController::class, 'store'])->name('store');
                        Route::get('/edit', [CountryController::class, 'edit'])->name('edit');
                        Route::post('/update', [CountryController::class, 'update'])->name('update');
                        Route::get('/destroy', [CountryController::class, 'destroy'])->name('destroy');
                    });
                Route::prefix('cities')
                    ->name('cities.')
                    ->group(function () {
                        Route::get('/', [CityController::class, 'index'])->name('index');
                        Route::get('/create', [CityController::class, 'create'])->name('create');
                        Route::post('/store', [CityController::class, 'store'])->name('store');
                        Route::post('/edit', [CityController::class, 'edit'])->name('edit');
                        Route::get('/update', [CityController::class, 'update'])->name('update');
                        Route::get('/destroy', [CityController::class, 'destroy'])->name('destroy');
                    });
                Route::prefix('theme')
                    ->name('theme.')
                    ->group(function () {
                        Route::get('/', [ThemeController::class, 'edit'])->name('index');
                        Route::get('/create', [ThemeController::class, 'create'])->name('create');
                        Route::post('/store', [ThemeController::class, 'store'])->name('store');
                        Route::get('/edit', [ThemeController::class, 'edit'])->name('edit');
                        Route::put('/update', [ThemeController::class, 'update'])->name('update');
                        Route::get('/destroy', [ThemeController::class, 'destroy'])->name('destroy');
                    });


            });
    });
