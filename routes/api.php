<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RoleController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::post('/coupons/validate', [CouponController::class, 'validate']);

Route::get('/ratings', [RatingController::class, 'index']);
Route::get('/ratings/{rating}', [RatingController::class, 'show']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Current user with roles and permissions
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('roles.permissions');
        return new UserResource($user);
    });

    // Get accessible menu items for current user
    Route::get('/user/menu', [MenuItemController::class, 'userMenu']);

    // Get user permissions
    Route::get('/user/permissions', function (Request $request) {
        return $request->user()->getAllPermissions()->pluck('slug');
    });

    // Addresses
    Route::apiResource('addresses', AddressController::class);

    // Orders
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    
    // Order Items (nested resource)
    Route::get('/orders/{order}/items', [OrderProductController::class, 'index']);
    Route::post('/orders/{order}/items', [OrderProductController::class, 'store']);
    Route::get('/orders/{order}/items/{item}', [OrderProductController::class, 'show']);
    Route::put('/orders/{order}/items/{item}', [OrderProductController::class, 'update']);
    Route::delete('/orders/{order}/items/{item}', [OrderProductController::class, 'destroy']);

    // Ratings
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::put('/ratings/{rating}', [RatingController::class, 'update']);
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);
    Route::get('/orders/{order}/rating', [RatingController::class, 'forOrder']);

    // Admin routes - require specific permissions
    Route::middleware(['permission:categories-create'])->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
    });
    Route::middleware(['permission:categories-edit'])->group(function () {
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
    });
    Route::middleware(['permission:categories-delete'])->group(function () {
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    Route::middleware(['permission:products-create'])->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
    });
    Route::middleware(['permission:products-edit'])->group(function () {
        Route::put('/products/{product}', [ProductController::class, 'update']);
    });
    Route::middleware(['permission:products-delete'])->group(function () {
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });

    // Coupons - require permission
    Route::middleware(['permission:coupons-view'])->group(function () {
        Route::get('/coupons', [CouponController::class, 'index']);
        Route::get('/coupons/{coupon}', [CouponController::class, 'show']);
    });
    Route::middleware(['permission:coupons-create'])->group(function () {
        Route::post('/coupons', [CouponController::class, 'store']);
    });
    Route::middleware(['permission:coupons-edit'])->group(function () {
        Route::put('/coupons/{coupon}', [CouponController::class, 'update']);
    });
    Route::middleware(['permission:coupons-delete'])->group(function () {
        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy']);
    });

    // Role & Permission management - admin/super-admin only
    Route::middleware(['role:admin,super-admin'])->group(function () {
        // Roles
        Route::apiResource('roles', RoleController::class);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions']);
        Route::post('/roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
        Route::post('/roles/{role}/menu-items', [RoleController::class, 'syncMenuItems']);

        // Permissions
        Route::apiResource('permissions', PermissionController::class);
        Route::get('/permissions-grouped', [PermissionController::class, 'grouped']);
        Route::get('/permission-modules', [PermissionController::class, 'modules']);

        // Menu Items
        Route::apiResource('menu-items', MenuItemController::class);
        Route::post('/menu-items/reorder', [MenuItemController::class, 'reorder']);
    });
});
