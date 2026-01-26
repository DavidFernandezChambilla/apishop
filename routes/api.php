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
Route::get('colors', function () {
    return response()->json(App\Models\Color::where('is_active', true)->get());
});
Route::get('sizes', function () {
    return response()->json(App\Models\Size::where('is_active', true)->get());
});

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
    });
});
