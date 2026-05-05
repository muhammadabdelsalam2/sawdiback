<?php

use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Api\Account\WalletController;
use App\Http\Controllers\Api\Plus\PlusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Setting\SupportController;

Route::prefix('v1/{locale}')->group(function () {

    // =========================================
    // ✅ Public — بدون auth
    // =========================================
    Route::get('categories', [App\Http\Controllers\Api\CategoriesController::class, 'index'])
        ->name('categories.index');

    Route::get('products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::get('products/show/{product}', [ProductController::class, 'show'])
        ->name('products.show');

    // =========================================
    // ✅ أي user مسجل — Cart + Favorites فقط
    // =========================================
    Route::middleware(['auth:sanctum'])->group(function () {

        // Favorites
        Route::prefix('products')->name('products.')
            ->controller(ProductController::class)
            ->group(function () {
                Route::prefix('{product}/favorite')->name('favorite.')->group(function () {
                    Route::post('add', 'addToFavorites')->name('favorite.add');
                    Route::delete('remove', 'removeFromFavorites')->name('favorite.remove');
                });
                Route::get('favorites', 'favorites')->name('favorites');
            });

        // Cart Routes
        Route::prefix('cart')->name('cart.')
            ->controller(App\Http\Controllers\Api\Ecommerce\CartController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('items', 'add')->name('items.add');
                Route::patch('items/{item}/increase', 'increase')->name('items.increase');
                Route::patch('items/{item}/decrease', 'decrease')->name('items.decrease');
                Route::patch('items/{item}', 'update')->name('items.update');
                Route::delete('items/{item}', 'remove')->name('items.remove');
                Route::delete('/', 'clear')->name('clear');
                Route::post('coupon', 'applyCoupon')->name('coupon.apply');
                Route::delete('coupon', 'removeCoupon')->name('coupon.remove');
                Route::patch('weekly-delivery', 'weeklyDelivery')->name('weekly');
                Route::patch('address', 'setAddress')->name('address.set');
            });
    });

    // =========================================
    // ✅ Full Access فقط — role: full-access
    // =========================================
    Route::middleware(['auth:sanctum', 'full.api.access'])->group(function () {

        // Address Routes
        Route::prefix('addresses')->name('addresses.')
            ->controller(App\Http\Controllers\Api\Ecommerce\AddressController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('manual', 'storeManual')->name('store.manual');
                Route::post('location', 'storeLocation')->name('store.location');
                Route::get('{address}', 'show')->name('show');
                Route::patch('{address}', 'update')->name('update');
                Route::delete('{address}', 'delete')->name('delete');
                Route::patch('{address}/default', 'setDefault')->name('default');
                Route::post('{address}/checkout', 'selectForCheckout')->name('checkout.select');
            });

        // Checkout Routes
        Route::prefix('checkout')->name('checkout.')
            ->controller(App\Http\Controllers\Api\Ecommerce\CheckoutController::class)
            ->group(function () {
                Route::get('summary', 'summary')->name('summary');
                Route::post('place-order', 'placeOrder')->name('place');
            });

        // Orders Routes
        Route::prefix('orders')->name('orders.')
            ->controller(App\Http\Controllers\Api\Ecommerce\OrderController::class)
            ->group(function () {
                Route::get('active', 'active')->name('active');
                Route::get('history', 'history')->name('history');
                Route::get('{order}', 'show')->name('show');
                Route::get('{order}/tracking', 'tracking')->name('tracking');
            });

        // Reviews Routes
        Route::prefix('reviews')->name('reviews.')
            ->controller(App\Http\Controllers\Api\Ecommerce\ReviewController::class)
            ->group(function () {
                Route::get('{order}', 'open')->name('open');
                Route::post('{order}', 'submit')->name('submit');
            });

        // Account Routes
        Route::prefix('account')->name('account.')
            ->controller(AccountController::class)
            ->group(function () {
                Route::get('/profile', 'overview')->name('profile.overview');
                Route::patch('/profile', 'updateProfile')->name('profile.update');
                Route::post('/contact/request-update', 'requestContactUpdate')->name('contact.requestUpdate');
                Route::post('/contact/verify-update', 'verifyContactUpdate')->name('contact.verifyUpdate');
                Route::get('/address-book', 'addressBook')->name('address_book.index');
                Route::post('/address-book', 'storeAddress')->name('address_book.store');
                Route::patch('/address-book/{address}', 'updateAddress')->name('address_book.update');
                Route::delete('/address-book/{address}', 'deleteAddress')->name('address_book.delete');
                Route::get('/payment-methods', 'paymentMethods')->name('payment_methods.index');
                Route::post('/payment-methods', 'storePaymentMethod')->name('payment_methods.store');
                Route::delete('/payment-methods/{paymentMethod}', 'deletePaymentMethod')->name('payment_methods.delete');
                Route::get('/notification-settings', 'notificationSettings')->name('notification_settings.show');
                Route::patch('/notification-settings', 'updateNotificationSettings')->name('notification_settings.update');
                Route::post('/logout', 'logout')->name('logout');
                Route::delete('/delete', 'destroy')->name('delete');
            });

        // Wallet Routes
        Route::prefix('account/wallet')->name('account.wallet.')
            ->controller(WalletController::class)
            ->group(function () {
                Route::get('/', 'show')->name('show');
                Route::post('/top-up', 'topUp')->name('top_up');
                Route::post('/convert-points', 'convertPoints')->name('convert_points');
            });

        // Plus Routes
        Route::prefix('plus')->name('plus.')
            ->controller(PlusController::class)
            ->group(function () {
                Route::get('/', 'overview')->name('overview');
                Route::get('/setup', 'setup')->name('setup');
                Route::post('/', 'store')->name('store');
                Route::get('/manage', 'manage')->name('manage');
                Route::get('/manage-subscription', 'manageSubscription')->name('manage_subscription.show');
                Route::patch('/manage-subscription', 'updateManageSubscription')->name('manage_subscription.update');
                Route::post('/skip', 'skip')->name('skip');
            });

        // Support Routes
        Route::prefix('support')->name('support.')
            ->controller(SupportController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('help-center', 'helpCenter')->name('help_center');
                Route::get('fqs', 'fqs')->name('fqs');
                Route::get('contact-us', 'contactUs')->name('contact_us');
                Route::get('terms-policies', 'termsPolicies')->name('terms_policies');
                Route::get('/{supportItem}/value', 'getValue')->name('value');
            });
    });
});
