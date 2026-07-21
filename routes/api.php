<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DriverController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/businesses', [BusinessController::class, 'index']);
Route::get('/businesses/{business}', [BusinessController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/businesses', [BusinessController::class, 'store']);
    Route::post('/businesses/{business}/products', [ProductController::class, 'store']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    Route::post('/orders/{order}/accept', [OrderController::class, 'acceptByBusiness']);
    Route::post('/orders/{order}/ready-for-pickup', [OrderController::class, 'readyForPickup']);

     Route::get('/business/orders', [OrderController::class, 'businessOrders']);

    Route::get('/driver/orders/available', [OrderController::class, 'availableForDrivers']);
    Route::post('/driver/orders/{order}/take', [OrderController::class, 'takeOrder']);
    Route::post('/driver/orders/{order}/picked-up', [OrderController::class, 'pickedUp']);
    Route::post('/driver/orders/{order}/delivered', [OrderController::class, 'delivered']);

        Route::post('/driver/orders/{order}/accept', [OrderController::class, 'acceptDriverOrder']);

    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);


    Route::post('/driver/location', [DriverController::class, 'updateLocation']);
    Route::get('/drivers/{driverId}/location', [DriverController::class, 'latestLocation']);
});