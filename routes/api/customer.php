<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\setting\SearchController;
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
use App\Http\Controllers\Customer\Procurement\SupplierController;
use App\Http\Controllers\Customer\Procurement\PurchaseRequisitionController;
use App\Http\Controllers\Customer\Procurement\RfqController;
use App\Http\Controllers\Customer\Procurement\QuotationController;
use App\Http\Controllers\Customer\Procurement\PurchaseOrderController;
use App\Http\Controllers\Customer\Procurement\GoodsReceiptController;
use App\Http\Controllers\Customer\Procurement\PurchaseInvoiceController;

Route::prefix('{locale}/customer/api')
    ->middleware(['set.locale','auth', 'role:Customer|SuperAdmin'])
    ->name('customer.api.')
    ->group(function () {
        //Login and Return Token 
        Route::get('/login', [LoginController::class, function(){
            return response()->json(['message' => 'Login successful']);
        }])->name('login')->withoutMiddleware('auth');

        // Global Search Route
    

        Route::get('/global-search', [SearchController::class, 'index'])
            ->name('global.search');

    });
