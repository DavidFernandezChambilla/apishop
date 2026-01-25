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
Route::get('categories', function () {
    return response()->json(App\Models\Category::where('is_active', true)->get());
});

Route::middleware('auth:api')->group(function () {
    // User routes
    Route::get('profile', [AuthController::class, 'me']);
});
