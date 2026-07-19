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
use App\Http\Controllers\Warehouse\InventoryCategoryController;
use App\Http\Controllers\Warehouse\InventoryProductController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Customer\Ecommerce\EcommerceOrderController;
use App\Http\Controllers\Customer\Farms\FarmController;
use App\Http\Controllers\Customer\Farms\FarmPenController;

use App\Http\Controllers\Customer\HR\DepartmentController;
use App\Http\Controllers\Customer\HR\JobTitleController;
use App\Http\Controllers\Customer\HR\EmployeeController;
use App\Http\Controllers\Customer\HR\AttendanceController;
use App\Http\Controllers\Customer\HR\LeaveRequestController;
use App\Http\Controllers\Customer\SalesDistribution\SalesContractController;
use App\Http\Controllers\Customer\SalesDistribution\SalesCustomerController;
use App\Http\Controllers\Customer\SalesDistribution\SalesDistributionDashboardController;
use App\Http\Controllers\Customer\SalesDistribution\SalesInvoiceController;
use App\Http\Controllers\Customer\SalesDistribution\SalesOrderController;
use App\Http\Controllers\Customer\SalesDistribution\SalesPaymentController;
use App\Http\Controllers\Customer\SalesDistribution\SalesShipmentController;
use App\Http\Controllers\Customer\Finance\FinanceDashboardController;
use App\Http\Controllers\Customer\Finance\ChartOfAccountsController;
use App\Http\Controllers\Customer\Finance\JournalEntryController;
use App\Http\Controllers\Customer\Finance\GeneralLedgerController;
use App\Http\Controllers\Customer\Finance\ExpenseController;
use App\Http\Controllers\Customer\Finance\ProfitLossController;
use App\Http\Controllers\Customer\Analytics\AnalyticsController;
use App\Http\Controllers\Customer\Warehouse\WarehouseAssetController;
use App\Http\Controllers\Customer\Procurement\SupplierController;
use App\Http\Controllers\Customer\Procurement\PurchaseRequisitionController;
use App\Http\Controllers\Customer\Procurement\RfqController;
use App\Http\Controllers\Customer\Procurement\QuotationController;
use App\Http\Controllers\Customer\Procurement\PurchaseOrderController;
use App\Http\Controllers\Customer\Procurement\GoodsReceiptController;
use App\Http\Controllers\Customer\Procurement\PurchaseInvoiceController;
use App\Http\Controllers\Customer\Poultry\BroilerCycleController;
use App\Http\Controllers\Customer\Poultry\ChickenBreedController;
use App\Http\Controllers\Customer\Poultry\HatcheryBatchController;
use App\Http\Controllers\Customer\Poultry\HatcheryMachineController;
use App\Http\Controllers\Customer\Poultry\LayerFlockController;
use App\Http\Controllers\Customer\Poultry\PoultryAlertController;
use App\Http\Controllers\Customer\Account\AccountController;
use App\Http\Controllers\setting\SearchController;

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['set.locale', 'auth', 'role:Customer|SuperAdmin'])
    ->name('customer.')
    ->group(function () {

        Route::get('/global-search', [SearchController::class, 'index'])
            ->name('global.search'); // Make it public for testing, can be protected later

        Route::prefix('account')->group(function () {
            Route::get('profile', [AccountController::class, 'profile'])->name('profile.show');
            Route::put('profile', [AccountController::class, 'updateProfile'])->name('profile.update');
            Route::get('password', [AccountController::class, 'password'])->name('password.show');
            Route::put('password', [AccountController::class, 'updatePassword'])->name('password.update');
        });

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

        Route::middleware(['permission:farms.view'])->group(function () {
            Route::post('farm-pens/{farm_pen}/financial-entries', [FarmPenController::class, 'storeFinancialEntry'])
                ->middleware(['permission:farms.manage'])
                ->name('farm-pens.financial-entries.store');
            Route::resource('farms', FarmController::class)
                ->middleware(['permission:farms.manage']);
            Route::resource('farm-pens', FarmPenController::class)
                ->parameters(['farm-pens' => 'farm_pen'])
                ->middleware(['permission:farms.manage']);
        });

        // =========================
        // Livestock (No Feature Gate)
        // =========================
        Route::prefix('livestock')->name('livestock.')->middleware(['permission:animals.view'])->group(function () {
            Route::resource('species', AnimalSpeciesController::class)->except(['show']);
            Route::resource('breeds', AnimalBreedController::class)->except(['show']);
            Route::resource('feed-types', FeedTypeController::class)->except(['show']);
            Route::resource('vaccines', VaccineController::class)->except(['show']);
            Route::post('vaccines/{vaccine}/batches', [VaccineController::class, 'storeBatch'])->name('vaccines.batches.store');

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

        Route::prefix('inventory')->name('inventory.')->middleware(['permission:inventory.view'])->group(function () {
            Route::resource('categories', InventoryCategoryController::class)->except(['show']);
            Route::resource('products', InventoryProductController::class)->except(['show']);

            Route::get('/', [WarehouseController::class, 'index'])->name('index');
            Route::post('batches', [WarehouseController::class, 'storeBatch'])->name('batches.store');
            Route::post('movements', [WarehouseController::class, 'storeMovement'])->name('movements.store');
            Route::post('production', [WarehouseController::class, 'storeProduction'])->name('production.store');
            Route::post('deliveries', [WarehouseController::class, 'storeDelivery'])->name('deliveries.store');

            Route::get('alerts', [WarehouseController::class, 'alerts'])->name('alerts.index');
            Route::get('traceability', [WarehouseController::class, 'traceability'])->name('traceability.index');
        });

        Route::prefix('warehouse-assets')->name('warehouse-assets.')->middleware(['permission:warehouse.view'])->group(function () {
            Route::get('/', [WarehouseAssetController::class, 'index'])->name('index');
             Route::middleware(['permission:warehouse.manage'])->group(function () {
                Route::get('/create', [WarehouseAssetController::class, 'create'])->name('create');
                Route::post('/', [WarehouseAssetController::class, 'store'])->name('store');
                Route::get('/{warehouse_asset}/edit', [WarehouseAssetController::class, 'edit'])->name('edit');
                Route::put('/{warehouse_asset}', [WarehouseAssetController::class, 'update'])->name('update');
                Route::delete('/{warehouse_asset}', [WarehouseAssetController::class, 'destroy'])->name('destroy');
            });
        });

        Route::prefix('analytics')->name('analytics.')->middleware(['permission:analytics.view'])->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        });

        Route::prefix('crops-feed')->name('crops-feed.')->middleware(['permission:crops.view'])->group(function () {
            Route::resource('crops', CropController::class);
            Route::post('crops/growth-stages', [CropController::class, 'storeGrowthStage'])->name('crops.growth-stages.store');
            Route::post('crops/cost-items', [CropController::class, 'storeCostItem'])->name('crops.cost-items.store');
            Route::post('crops/material-usages', [CropController::class, 'storeMaterialUsage'])->name('crops.material-usages.store');

            Route::get('feed', [FeedManagementController::class, 'index'])->name('feed.index');
            Route::post('feed/seedling-stocks', [FeedManagementController::class, 'storeSeedlingStock'])->name('feed.seedling-stocks.store');
            Route::post('feed/stock-movements', [FeedManagementController::class, 'storeStockMovement'])->name('feed.stock-movements.store');
            Route::post('feed/consumptions', [FeedManagementController::class, 'storeConsumption'])->name('feed.consumptions.store');
            Route::post('feed/crop-allocations', [FeedManagementController::class, 'storeCropAllocation'])->name('feed.crop-allocations.store');

            Route::get('reports', [FeedManagementController::class, 'reports'])->name('reports.index');
        });

        Route::prefix('poultry')
            ->name('poultry.')
            ->middleware(['permission:poultry.view'])
            ->group(function () {
                Route::get('alerts', PoultryAlertController::class)->name('alerts.index');

                Route::resource('broiler-cycles', BroilerCycleController::class)
                    ->parameters(['broiler-cycles' => 'broiler_cycle'])
                    ->middleware(['create' => 'permission:poultry.manage', 'store' => 'permission:poultry.manage', 'edit' => 'permission:poultry.manage', 'update' => 'permission:poultry.manage', 'destroy' => 'permission:poultry.manage']);
                Route::post('broiler-cycles/{broiler_cycle}/mortalities', [BroilerCycleController::class, 'storeMortality'])
                    ->middleware(['permission:poultry.manage'])
                    ->name('broiler-cycles.mortalities.store');
                Route::post('broiler-cycles/{broiler_cycle}/sales', [BroilerCycleController::class, 'storeSale'])
                    ->middleware(['permission:poultry.manage'])
                    ->name('broiler-cycles.sales.store');
                Route::post('broiler-cycles/{broiler_cycle}/costs', [BroilerCycleController::class, 'storeCost'])
                    ->middleware(['permission:poultry.manage'])
                    ->name('broiler-cycles.costs.store');

                Route::resource('layer-flocks', LayerFlockController::class)
                    ->parameters(['layer-flocks' => 'layer_flock'])
                    ->middleware(['create' => 'permission:poultry.manage', 'store' => 'permission:poultry.manage', 'edit' => 'permission:poultry.manage', 'update' => 'permission:poultry.manage', 'destroy' => 'permission:poultry.manage']);
                Route::post('layer-flocks/{layer_flock}/egg-logs', [LayerFlockController::class, 'storeEggLog'])
                    ->middleware(['permission:poultry.manage'])
                    ->name('layer-flocks.egg-logs.store');
                Route::post('layer-flocks/{layer_flock}/mortalities', [LayerFlockController::class, 'storeMortality'])
                    ->middleware(['permission:poultry.manage'])
                    ->name('layer-flocks.mortalities.store');

                Route::resource('hatchery-machines', HatcheryMachineController::class)
                    ->except(['show'])
                    ->parameters(['hatchery-machines' => 'hatchery_machine'])
                    ->middleware(['create' => 'permission:poultry.manage', 'store' => 'permission:poultry.manage', 'edit' => 'permission:poultry.manage', 'update' => 'permission:poultry.manage', 'destroy' => 'permission:poultry.manage']);
                Route::resource('hatchery-batches', HatcheryBatchController::class)
                    ->parameters(['hatchery-batches' => 'hatchery_batch'])
                    ->middleware(['create' => 'permission:poultry.manage', 'store' => 'permission:poultry.manage', 'edit' => 'permission:poultry.manage', 'update' => 'permission:poultry.manage', 'destroy' => 'permission:poultry.manage']);

                Route::resource('chicken-breeds', ChickenBreedController::class)
                    ->parameters(['chicken-breeds' => 'chicken_breed'])
                    ->middleware(['create' => 'permission:poultry.manage', 'store' => 'permission:poultry.manage', 'edit' => 'permission:poultry.manage', 'update' => 'permission:poultry.manage', 'destroy' => 'permission:poultry.manage']);
                Route::post('chicken-breeds/{chicken_breed}/egg-logs', [ChickenBreedController::class, 'storeEggLog'])
                    ->middleware(['permission:poultry.manage'])
                    ->name('chicken-breeds.egg-logs.store');
            });

        Route::prefix('sales-distribution')->name('sales-distribution.')->middleware(['permission:sales.view'])->group(function () {
            Route::get('/', [SalesDistributionDashboardController::class, 'index'])->name('dashboard');

            Route::resource('customers', SalesCustomerController::class);
            Route::resource('contracts', SalesContractController::class);
            Route::resource('orders', SalesOrderController::class);
            Route::resource('shipments', SalesShipmentController::class);
            Route::resource('invoices', SalesInvoiceController::class);

            Route::post('invoices/{invoice}/payments', [SalesPaymentController::class, 'store'])->name('invoices.payments.store');
            Route::put('invoices/{invoice}/payments/{payment}', [SalesPaymentController::class, 'update'])->name('invoices.payments.update');
            Route::delete('invoices/{invoice}/payments/{payment}', [SalesPaymentController::class, 'destroy'])->name('invoices.payments.destroy');
        });

        // =========================
        // Procurement
        // =========================
        Route::prefix('procurement')->name('procurement.')->middleware(['permission:procurement.view'])->group(function () {
            Route::resource('suppliers', SupplierController::class)->except(['show']);
            Route::resource('requisitions', PurchaseRequisitionController::class);
            Route::resource('rfqs', RfqController::class);
            Route::resource('quotations', QuotationController::class);
            Route::resource('purchase-orders', PurchaseOrderController::class)
                ->parameters(['purchase-orders' => 'order']);
            Route::resource('goods-receipts', GoodsReceiptController::class)
                ->parameters(['goods-receipts' => 'receipt']);
            Route::resource('invoices', PurchaseInvoiceController::class);
        });

        // =========================
        // Finance
        // =========================
        Route::prefix('finance')->name('finance.')->middleware(['permission:finance.view'])->group(function () {
            Route::get('/', [FinanceDashboardController::class, 'index'])->name('dashboard');

            Route::resource('accounts', ChartOfAccountsController::class)->except(['show']);
            Route::resource('journal-entries', JournalEntryController::class)->only(['index', 'create', 'store', 'show']);
            Route::get('ledger', [GeneralLedgerController::class, 'index'])->name('ledger.index');
            Route::resource('expenses', ExpenseController::class)->except(['show']);
            Route::get('profit-loss', [ProfitLossController::class, 'index'])->name('profit-loss.index');
        });

        // =========================
        // E-Commerce Orders (Dashboard)
        // =========================
        Route::prefix('ecommerce')
            ->name('ecommerce.')
            ->middleware(['permission:ecommerce.view'])
            ->group(function () {
            Route::get('orders', [EcommerceOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [EcommerceOrderController::class, 'show'])->name('orders.show');
            Route::post('orders/{order}/status', [EcommerceOrderController::class, 'updateStatus'])->name('orders.status');
        });

        // =========================
        // HR Management (Feature Gated)
        // =========================
        Route::prefix('hr')
            ->name('hr.')
            ->middleware(['feature:hr_management', 'permission:hr.view'])
            ->group(function () {

            Route::get('/', fn() => redirect()->route('customer.hr.employees.index', ['locale' => request()->route('locale')]))
                ->name('index');

            Route::resource('departments', DepartmentController::class)->except(['show']);
            Route::resource('job-titles', JobTitleController::class)->except(['show']);
            Route::get('employees/document-alerts', [EmployeeController::class, 'documentAlerts'])
                ->name('employees.document-alerts');
            Route::resource('employees', EmployeeController::class);

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
