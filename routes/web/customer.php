<?php
use App\Http\Controllers\DashboardController;
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


Route::prefix('{locale}/customer')
    ->where(['locale' => '[a-z]{2}-[A-Z]{2}'])
    ->middleware(['web', 'set.locale'])
    ->group(function () {

        // Customer dashboard route
        Route::get('/ziad', [DashboardController::class, 'index'])
            ->name('customer.dashboard');

    });
