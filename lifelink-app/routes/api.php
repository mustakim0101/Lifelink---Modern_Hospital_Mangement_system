<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AccountControlController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api', 'active.user'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

Route::prefix('dev')->group(function () {
    Route::post('/create-admin', [AuthController::class, 'createAdmin']);
});

Route::prefix('admin')->middleware(['auth:api', 'active.user', 'role:Admin'])->group(function () {
    Route::post('/users/{user}/freeze', [AccountControlController::class, 'freeze']);
    Route::post('/users/{user}/unfreeze', [AccountControlController::class, 'unfreeze']);
    Route::get('/users/{user}/status', [AccountControlController::class, 'status']);
});
