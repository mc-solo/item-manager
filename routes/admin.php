<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin only. The can:manage-users gate is the server check, so a
// staff user hitting these URLs directly gets a 403.
Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
});
