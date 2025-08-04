<?php

use App\Http\Controllers\Device\DeviceController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('devices', 'devices/index');


    Route::get('devices/index', [DeviceController::class, 'index'])->name('devices.index');
});
