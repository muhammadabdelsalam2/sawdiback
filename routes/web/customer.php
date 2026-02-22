<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Customer\Subscriptions\CustomerSubscriptionController;
use App\Http\Controllers\Livestock\AnimalBreedController;
use App\Http\Controllers\Livestock\AnimalSpeciesController;
use App\Http\Controllers\Livestock\FeedTypeController;
use App\Http\Controllers\Livestock\LivestockAnimalController;
use App\Http\Controllers\Livestock\LivestockOperationsController;
use App\Http\Controllers\Livestock\VaccineController;
use App\Http\Controllers\CropsFeed\CropController;
use App\Http\Controllers\CropsFeed\FeedManagementController;

use App\Http\Controllers\Customer\HR\DepartmentController;
use App\Http\Controllers\Customer\HR\JobTitleController;
use App\Http\Controllers\Customer\HR\EmployeeController;
use App\Http\Controllers\Customer\HR\AttendanceController;
use App\Http\Controllers\Customer\HR\LeaveRequestController;

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['set.locale', 'auth', 'role:Customer|SuperAdmin'])
    ->name('customer.')
    ->group(function () {

        // =========================
        // Subscription (Always Allowed)
        // =========================
        Route::get('subscription', [CustomerSubscriptionController::class, 'index'])
            ->name('subscription.index');

        Route::post('subscription/subscribe', [CustomerSubscriptionController::class, 'subscribe'])
            ->name('subscription.subscribe');

        Route::post('subscription/change-plan', [CustomerSubscriptionController::class, 'changePlan'])
            ->name('subscription.change-plan');

        Route::post('subscription/cancel', [CustomerSubscriptionController::class, 'cancel'])
            ->name('subscription.cancel');

        // =========================
        // Livestock (No Feature Gate)
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

        Route::prefix('crops-feed')->name('crops-feed.')->group(function () {
            Route::resource('crops', CropController::class);
            Route::post('crops/growth-stages', [CropController::class, 'storeGrowthStage'])->name('crops.growth-stages.store');
            Route::post('crops/cost-items', [CropController::class, 'storeCostItem'])->name('crops.cost-items.store');

            Route::get('feed', [FeedManagementController::class, 'index'])->name('feed.index');
            Route::post('feed/stock-movements', [FeedManagementController::class, 'storeStockMovement'])->name('feed.stock-movements.store');
            Route::post('feed/consumptions', [FeedManagementController::class, 'storeConsumption'])->name('feed.consumptions.store');
            Route::post('feed/crop-allocations', [FeedManagementController::class, 'storeCropAllocation'])->name('feed.crop-allocations.store');

            Route::get('reports', [FeedManagementController::class, 'reports'])->name('reports.index');
        });

        Route::prefix('crops-feed')->name('crops-feed.')->group(function () {
            Route::resource('crops', CropController::class);
            Route::post('crops/growth-stages', [CropController::class, 'storeGrowthStage'])->name('crops.growth-stages.store');
            Route::post('crops/cost-items', [CropController::class, 'storeCostItem'])->name('crops.cost-items.store');

            Route::get('feed', [FeedManagementController::class, 'index'])->name('feed.index');
            Route::post('feed/stock-movements', [FeedManagementController::class, 'storeStockMovement'])->name('feed.stock-movements.store');
            Route::post('feed/consumptions', [FeedManagementController::class, 'storeConsumption'])->name('feed.consumptions.store');
            Route::post('feed/crop-allocations', [FeedManagementController::class, 'storeCropAllocation'])->name('feed.crop-allocations.store');

            Route::get('reports', [FeedManagementController::class, 'reports'])->name('reports.index');
        });

        // =========================
        // HR Management (Feature Gated)
        // =========================
        Route::prefix('hr')
            ->name('hr.')
            ->middleware(['feature:hr_management'])
            ->group(function () {

                Route::get('/', fn() => redirect()->route('customer.hr.employees.index', ['locale' => request()->route('locale')]))
                    ->name('index');

                Route::resource('departments', DepartmentController::class)->except(['show']);
                Route::resource('job-titles', JobTitleController::class)->except(['show']);
                Route::resource('employees', EmployeeController::class)->except(['show']);

                // Attendance
                Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
                Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
                Route::post('attendance/{attendance}/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

                // Leaves
                Route::get('leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
                Route::get('leaves/create', [LeaveRequestController::class, 'create'])->name('leaves.create');
                Route::post('leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');
                Route::post('leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
                Route::post('leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
            });
    });
