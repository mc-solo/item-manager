<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin only. The can:manage-users gate is the server check, so a
// technician hitting these URLs directly gets a 403.
Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
    Route::put('admin/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});
