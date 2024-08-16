<?php
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// User routes with 'users' prefix
Route::prefix('users')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('reset-password', [UserController::class, 'resetPassword']);
});

Route::middleware('auth:api')->group(function () {
    Route::Resource('users', UserController::class)->except(['index', 'create', 'store', 'edit']);
    // Product routes
    Route::apiResource('products', ProductController::class)->except(['index', 'create', 'edit']);
});

