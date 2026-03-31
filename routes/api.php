<?php

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/featured', [RecipeController::class, 'featured']);
Route::get('/recipes/{slug}', [RecipeController::class, 'show']);



// Payment webhook (no auth — called by Midtrans)
Route::post('/payment/webhook', [PaymentWebhookController::class, 'handle']);

// Authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/rate', [OrderController::class, 'rate']);

    // Messages
    Route::get('/orders/{order}/messages', [MessageController::class, 'index']);
    Route::post('/orders/{order}/messages', [MessageController::class, 'store']);
});

// Driver routes
Route::prefix('driver')->middleware(['auth:sanctum', 'driver'])->group(function () {
    Route::get('orders', [DriverController::class, 'index']);
    Route::get('orders/available', [DriverController::class, 'availableOrders']);
    Route::post('orders/{order}/pickup', [DriverController::class, 'pickupOrder']);
    Route::post('orders/{order}/deliver', [DriverController::class, 'deliverOrder']);
});

// Admin API routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);
    
    // Users Management
    Route::get('users', [App\Http\Controllers\Admin\UserController::class, 'index']);
    Route::patch('users/{user}/role', [App\Http\Controllers\Admin\UserController::class, 'updateRole']);
    Route::delete('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
    
    // Settings
    Route::put('settings/profile', [App\Http\Controllers\Admin\SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [App\Http\Controllers\Admin\SettingsController::class, 'updatePassword']);

    // Products & Orders
    Route::apiResource('products', AdminProductController::class);
    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('orders/{order}', [AdminOrderController::class, 'show']);
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
});
