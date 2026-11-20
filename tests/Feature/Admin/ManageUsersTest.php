<?php

use App\Livewire\Auth\Login;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('admin can create a technical staff user, staff can log in with username', function () {
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'boss']);

    $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Miki',
        'username' => 'miki',
        'email' => 'miki@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'staff',
    ])->assertRedirect();

    $staff = User::where('username', 'miki')->first();
    expect($staff)->not->toBeNull()
        ->and($staff->role)->toBe('staff')
        ->and(Hash::check('password123', $staff->password))->toBeTrue();

    Livewire::test(Login::class)
        ->set('login', 'miki')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($staff);
});

test('staff can also log in with email', function () {
    $staff = User::factory()->create(['role' => 'staff', 'username' => 'mesi']);

    Livewire::test(Login::class)
        ->set('login', $staff->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors();

    $this->assertAuthenticatedAs($staff);
});

test('staff cannot open Manage Users or create users, admin can', function () {
    $staff = User::factory()->create(['role' => 'staff', 'username' => 'miki']);
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'boss']);

    $this->actingAs($staff)->get('/admin/users')->assertForbidden();
    $this->actingAs($staff)->post('/admin/users', [
        'name' => 'Nope',
        'username' => 'nope',
        'email' => 'nope@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'staff',
    ])->assertForbidden();

    $this->actingAs($admin)->get('/admin/users')->assertOk();
});

test('admin can reset a staff password and delete a staff user, but not themselves', function () {
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'boss']);
    $staff = User::factory()->create(['role' => 'staff', 'username' => 'miki']);

    $this->actingAs($admin)->put("/admin/users/{$staff->id}/reset-password", [
        'password' => 'newpassword123',
    ])->assertRedirect();
    expect(Hash::check('newpassword123', $staff->fresh()->password))->toBeTrue();

    $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertRedirect();
    expect(User::find($admin->id))->not->toBeNull();

    $this->actingAs($admin)->delete("/admin/users/{$staff->id}")->assertRedirect();
    expect(User::find($staff->id))->toBeNull();
});

test('admin cannot delete the last admin', function () {
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'boss']);
    $otherAdmin = User::factory()->create(['role' => 'admin', 'username' => 'boss2']);

    $this->actingAs($admin)->delete("/admin/users/{$otherAdmin->id}")->assertRedirect();
    expect(User::find($otherAdmin->id))->toBeNull();

    $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertRedirect();
    expect(User::find($admin->id))->not->toBeNull();
});

test('staff cannot delete a device from the list, admin can', function () {
    $staff = User::factory()->create(['role' => 'staff', 'username' => 'miki']);
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'boss']);
    $device = Device::factory()->create();

    $this->actingAs($staff)->delete("/devices/delete/{$device->id}")->assertForbidden();
    expect(Device::find($device->id))->not->toBeNull();

    $this->actingAs($admin)->delete("/devices/delete/{$device->id}")->assertRedirect('/devices/index');
    expect(Device::find($device->id))->toBeNull();
});

test('admin can bulk delete selected devices, staff cannot', function () {
    $staff = User::factory()->create(['role' => 'staff', 'username' => 'miki']);
    $admin = User::factory()->create(['role' => 'admin', 'username' => 'boss']);
    $devices = Device::factory()->count(2)->create();

    $this->actingAs($staff)->post('/devices/bulk', [
        'ids' => $devices->pluck('id')->all(),
        'action' => 'delete',
    ])->assertForbidden();

    $this->actingAs($admin)->postJson('/devices/bulk', [
        'ids' => $devices->pluck('id')->all(),
        'action' => 'delete',
    ])->assertOk();
    expect(Device::count())->toBe(0);
});
