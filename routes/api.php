<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DeviceController;
use Illuminate\Support\Facades\Route;

// API v1. All endpoints live under /api/v1. There are no unversioned
// /api/* routes: breaking changes go to v2, v1 stays stable.
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/technicians', [DeviceController::class, 'technicians']);
        Route::get('/devices', [DeviceController::class, 'index']);
        Route::post('/devices', [DeviceController::class, 'store']);
        Route::get('/devices/{device}', [DeviceController::class, 'show']);
        Route::put('/devices/{device}', [DeviceController::class, 'update']);
        Route::patch('/devices/{device}/status', [DeviceController::class, 'updateStatus']);
        Route::post('/devices/{device}/assign', [DeviceController::class, 'assign']);
        Route::post('/devices/{device}/complete', [DeviceController::class, 'complete']);
        Route::delete('/devices/{device}', [DeviceController::class, 'destroy']);
    });
});
