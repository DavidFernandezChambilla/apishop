<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

// Ecommerce Routes
Route::get('products', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('products/{slug}', [App\Http\Controllers\ProductController::class, 'show']);
Route::get('categories', [App\Http\Controllers\CategoryController::class, 'index']);
Route::get('subcategories', [App\Http\Controllers\SubcategoryController::class, 'index']);
Route::get('colors', [App\Http\Controllers\ColorController::class, 'index']);
Route::get('sizes', [App\Http\Controllers\SizeController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    // User routes
    Route::get('profile', [AuthController::class, 'me']);
    Route::get('orders', [App\Http\Controllers\OrderController::class, 'index']);
    Route::post('orders', [App\Http\Controllers\OrderController::class, 'store']);
    Route::get('orders/{id}', [App\Http\Controllers\OrderController::class, 'show']);

    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::post('products', [App\Http\Controllers\ProductController::class, 'store']);
        Route::put('products/{id}', [App\Http\Controllers\ProductController::class, 'update']);
        Route::delete('products/{id}', [App\Http\Controllers\ProductController::class, 'destroy']);

        // Categories Map
        Route::post('categories', [App\Http\Controllers\CategoryController::class, 'store']);
        Route::get('categories/show/{id}', [App\Http\Controllers\CategoryController::class, 'show']);
        Route::put('categories/{id}', [App\Http\Controllers\CategoryController::class, 'update']);
        Route::delete('categories/{id}', [App\Http\Controllers\CategoryController::class, 'destroy']);

        // Subcategories Map
        Route::post('subcategories', [App\Http\Controllers\SubcategoryController::class, 'store']);
        Route::get('subcategories/show/{id}', [App\Http\Controllers\SubcategoryController::class, 'show']);
        Route::put('subcategories/{id}', [App\Http\Controllers\SubcategoryController::class, 'update']);
        Route::delete('subcategories/{id}', [App\Http\Controllers\SubcategoryController::class, 'destroy']);

        // Colors Map
        Route::get('colors/all', [App\Http\Controllers\ColorController::class, 'all']);
        Route::post('colors', [App\Http\Controllers\ColorController::class, 'store']);
        Route::get('colors/show/{id}', [App\Http\Controllers\ColorController::class, 'show']);
        Route::put('colors/{id}', [App\Http\Controllers\ColorController::class, 'update']);
        Route::delete('colors/{id}', [App\Http\Controllers\ColorController::class, 'destroy']);

        // Sizes Map
        Route::get('sizes/all', [App\Http\Controllers\SizeController::class, 'all']);
        Route::post('sizes', [App\Http\Controllers\SizeController::class, 'store']);
        Route::get('sizes/show/{id}', [App\Http\Controllers\SizeController::class, 'show']);
        Route::put('sizes/{id}', [App\Http\Controllers\SizeController::class, 'update']);
        Route::delete('sizes/{id}', [App\Http\Controllers\SizeController::class, 'destroy']);

        // Orders Map
        Route::get('admin/orders', [App\Http\Controllers\OrderController::class, 'adminIndex']);
        Route::get('admin/orders/{id}', [App\Http\Controllers\OrderController::class, 'adminShow']);
        Route::put('admin/orders/{id}/status', [App\Http\Controllers\OrderController::class, 'updateStatus']);
        // Settings Map
        Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->withoutMiddleware(['auth:api', 'admin']);
        Route::post('settings', [App\Http\Controllers\SettingController::class, 'update']);
    });
});
