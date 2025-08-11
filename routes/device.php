<?php

use App\Http\Controllers\Device\DeviceController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // redirect to devices index
    Route::redirect('devices', 'devices/index');


    Route::get('devices/index', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('devices/show/{device}', [DeviceController::class, 'show'])->name('device.show');
    Route::get('devices/edit/{device}', [DeviceController::class, 'edit'])->name('device.edit');
    Route::post('devices/store', [DeviceController::class, 'store'])->name('device.store');
    Route::put('devices/update/{device}', [DeviceController::class, 'update'])->name('device.update');
    Route::delete('devices/delete/{device}', [DeviceController::class, 'destroy'])->name('device.destroy');
    Route::get('devices/create', function () {
        return view('livewire.devices.device-create');
    })->name('device.create');

});
