<?php

use App\Http\Controllers\Mobile\StripePaymentController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Livewire\Mobile\Business\Dashboard as BusinessDashboard;
use App\Livewire\Mobile\Business\Orders as BusinessOrders;
use App\Livewire\Mobile\Business\Products as BusinessProducts;
use App\Livewire\Mobile\Business\Profile as BusinessProfile;
use App\Livewire\Mobile\Cart;
use App\Livewire\Mobile\Checkout;
use App\Livewire\Mobile\Driver\Dashboard as DriverDashboard;
use App\Livewire\Mobile\Driver\Delivery as DriverDelivery;
use App\Livewire\Mobile\Home;
use App\Livewire\Mobile\Orders;
use App\Livewire\Mobile\OrderShow;
use App\Livewire\Mobile\OrderTracking;
use App\Livewire\Mobile\Payment;
use App\Livewire\Mobile\ProductShow;
use App\Livewire\Mobile\Profile;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::prefix('app')->name('mobile.')->group(function () {
    Route::get('/', Home::class)->name('home');
    Route::get('/products/{product}', ProductShow::class)->name('products.show');
    Route::get('/cart', Cart::class)->name('cart');

    Route::middleware('auth')->group(function () {
        Route::get('/checkout', Checkout::class)->name('checkout');
        Route::get('/payment/{order}', Payment::class)->name('payment');
        Route::post('/payment/{order}/intent', [StripePaymentController::class, 'intent'])->name('payment.intent');
        Route::get('/orders', Orders::class)->name('orders');
        Route::get('/orders/{order}', OrderShow::class)->name('orders.show');
        Route::get('/orders/{order}/tracking', OrderTracking::class)->name('orders.tracking');
        Route::get('/profile', Profile::class)->name('profile');

        Route::prefix('business')->name('business.')->group(function () {
            Route::get('/', BusinessDashboard::class)->name('dashboard');
            Route::get('/profile', BusinessProfile::class)->name('profile');
            Route::get('/products', BusinessProducts::class)->name('products');
            Route::get('/orders', BusinessOrders::class)->name('orders');
        });

        Route::prefix('driver')->name('driver.')->group(function () {
            Route::get('/', DriverDashboard::class)->name('dashboard');
            Route::get('/delivery/{order}', DriverDelivery::class)->name('delivery');
        });
    });
});
