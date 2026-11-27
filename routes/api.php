<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceController;
use Illuminate\Support\Facades\Route;

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
