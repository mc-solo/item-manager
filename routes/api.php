<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\DeviceController;

Route::apiResource('devices', DeviceController::class);

