<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\ApplicationReviewController;
use App\Http\Controllers\Api\Admin\AccountControlController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\WardCatalogController;
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

Route::prefix('admin')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::get('/applications', [ApplicationReviewController::class, 'index']);
    Route::post('/applications/{application}/approve', [ApplicationReviewController::class, 'approve']);
    Route::post('/applications/{application}/reject', [ApplicationReviewController::class, 'reject']);
});

Route::prefix('applications')->middleware(['auth:api', 'active.user'])->group(function () {
    Route::post('/', [JobApplicationController::class, 'submit']);
    Route::get('/my', [JobApplicationController::class, 'myApplications']);
    Route::get('/my/latest', [JobApplicationController::class, 'myLatest']);
});

Route::prefix('ward')->middleware(['auth:api', 'active.user'])->group(function () {
    Route::get('/departments', [WardCatalogController::class, 'departments']);
    Route::get('/care-units', [WardCatalogController::class, 'careUnits']);
    Route::get('/beds', [WardCatalogController::class, 'beds']);
    Route::get('/beds/summary', [WardCatalogController::class, 'bedSummary']);
});

Route::prefix('ward')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::post('/care-units', [WardCatalogController::class, 'storeCareUnit']);
    Route::post('/beds', [WardCatalogController::class, 'storeBed']);
});
