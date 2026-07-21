<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Mobile\Business\Dashboard as BusinessDashboard;
use App\Livewire\Mobile\Business\Orders as BusinessOrders;
use App\Livewire\Mobile\Business\Products as BusinessProducts;
use App\Livewire\Mobile\Business\Profile as BusinessProfile;
use App\Livewire\Mobile\Customer\Home as CustomerHome;
use App\Livewire\Mobile\Customer\Cart as CustomerCart;
use App\Livewire\Mobile\Customer\Checkout as CustomerCheckout;
use App\Livewire\Mobile\Customer\ProductShow;
use App\Livewire\Mobile\Customer\Orders as CustomerOrders;
use App\Livewire\Mobile\Customer\OrderShow   as CustomerOrderShow;
use App\Livewire\Mobile\Customer\Profile as CustomerProfile;
use App\Livewire\Mobile\Driver\Dashboard as DriverDashboard;
use App\Livewire\Mobile\Driver\Orders as DriverOrders;
use App\Livewire\Mobile\Driver\Delivery as DriverDelivery;
use App\Livewire\Mobile\Driver\Profile as DriverProfile;
use App\Livewire\Mobile\Driver\Wallet as DriverWallet;
use App\Livewire\Mobile\Driver\WalletSuccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeDriverWalletWebhookController;

/*
|--------------------------------------------------------------------------
| Ruta principal
|--------------------------------------------------------------------------
|
| No debe estar dentro del middleware guest.
| Si el usuario no está autenticado, lo envía al login.
| Si ya está autenticado, lo envía según su role.
|
*/

Route::post(
    '/stripe/webhooks/driver-wallet',
    StripeDriverWalletWebhookController::class
)->name('stripe.webhooks.driver-wallet');

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'customer' => redirect()->route('customer.home'),
        'business' => redirect()->route('business.dashboard'),
        'driver' => redirect()->route('driver.dashboard'),
        default => abort(403, 'El usuario no tiene un rol válido.'),
    };
})->name('home');

/*
|--------------------------------------------------------------------------
| Rutas para invitados
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)
        ->name('login');

    Route::get('/register', Register::class)
        ->name('register');

    Route::get('/forgot-password', ForgotPassword::class)
        ->name('password.request');

    Route::get('/reset-password/{token}', ResetPassword::class)
        ->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Cerrar sesión
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Cliente
    |--------------------------------------------------------------------------
    */

    Route::get('/app', CustomerHome::class)
        ->name('customer.home');

         Route::get('/app/profile', CustomerProfile::class)
        ->name('customer.profile');

    Route::get('/app/orders', CustomerOrders::class)
        ->name('customer.orders');

        Route::get('/app/cart', CustomerCart::class)
    ->name('customer.cart');

    Route::get('/app/checkout', CustomerCheckout::class)
    ->name('customer.checkout');

    Route::get('/app/products/{product}', ProductShow::class)
    ->name('customer.products.show');

        Route::get('/app/orders/{order}', CustomerOrderShow::class)
    ->name('customer.orders.show');

    /*
    |--------------------------------------------------------------------------
    | Negocio
    |--------------------------------------------------------------------------
    */

    Route::get('/business', BusinessDashboard::class)
        ->name('business.dashboard');

    Route::get('/business/orders', BusinessOrders::class)
        ->name('business.orders');

           Route::get('/business/profile', BusinessProfile::class)
        ->name('business.profile');
          Route::get('/business/products', BusinessProducts::class)
        ->name('business.products');

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    */

    Route::get('/driver', DriverDashboard::class)
        ->name('driver.dashboard');

        Route::get('/driver/profile', DriverProfile::class)
        ->name('driver.profile');

         Route::get('/driver/orders', DriverOrders::class)
        ->name('driver.orders');

    Route::get('/driver/order/{order}', DriverDelivery::class)
        ->name('driver.delivery');

         Route::get('/driver/wallet', DriverWallet::class)
            ->name('wallet.index');

        Route::get('/wallet/success', WalletSuccess::class)
            ->name('wallet.success');

       
});