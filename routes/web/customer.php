<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Livestock\AnimalBreedController;
use App\Http\Controllers\Livestock\AnimalSpeciesController;
use App\Http\Controllers\Livestock\FeedTypeController;
use App\Http\Controllers\Livestock\LivestockAnimalController;
use App\Http\Controllers\Livestock\LivestockOperationsController;
use App\Http\Controllers\Livestock\VaccineController;
use Illuminate\Support\Facades\Route;

/**
 * --------------------------------------------------------------------------
 * Customer Routes
 * --------------------------------------------------------------------------
 *
 * This file contains all routes related to the "Customer" section of the system.
 * 
 * - All routes are prefixed with the locale and '/customer', e.g., /en-SA/customer/ziad
 * - Middleware applied:
 *      - 'web'        => Laravel web middleware group (sessions, CSRF, etc.)
 *      - 'set.locale' => Sets the application locale from the route parameter
 *
 * Naming convention:
 *      - Route names start with 'customer.' (e.g., customer.dashboard)
 *
 * Add all new customer-related routes here to keep them organized and maintainable.
 */


Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['set.locale', 'auth', 'role:Customer|SuperAdmin'])
    ->name('customer.')
    ->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

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
