<?php

use App\Models\Device;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('technician cannot delete a device, admin can', function () {
    $staff = User::factory()->create(['role' => 'hardware_technician']);
    $admin = User::factory()->create(['role' => 'admin']);
    $device = Device::factory()->create();

    $this->actingAs($staff)->delete("/devices/delete/{$device->id}")->assertForbidden();
    expect(Device::find($device->id))->not->toBeNull();

    $this->actingAs($admin)->delete("/devices/delete/{$device->id}")->assertRedirect('/devices/index');
    expect(Device::find($device->id))->toBeNull();
});

test('technician can create, edit and complete devices', function () {
    $staff = User::factory()->create(['role' => 'hardware_technician']);
    $device = Device::factory()->create(['repair_status' => 'pending', 'assigned_to' => $staff->id]);

    $this->actingAs($staff)->post('/devices/store', [
        'client_name' => 'Miki',
        'client_number' => '0912345678',
        'amount' => 1000,
        'device_type' => 'mobile',
        'device_model' => 'iPhone 13',
        'repair_status' => 'pending',
    ])->assertRedirect();

    $this->actingAs($staff)->post("/devices/{$device->id}/complete")->assertRedirect();
    expect($device->fresh()->repair_status)->toBe('completed');
});

test('technician cannot open the admin user list, admin can', function () {
    $staff = User::factory()->create(['role' => 'hardware_technician']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($staff)->get('/admin/users')->assertForbidden();
    $this->actingAs($admin)->get('/admin/users')->assertOk();
});

test('admin can promote a technician user, but cannot demote themselves', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'hardware_technician']);

    $this->actingAs($admin)->put("/admin/users/{$staff->id}/role", ['role' => 'admin'])->assertRedirect();
    expect($staff->fresh()->role)->toBe('admin');

    $this->actingAs($admin)->put("/admin/users/{$admin->id}/role", ['role' => 'hardware_technician'])->assertRedirect();
    expect($admin->fresh()->role)->toBe('admin');
});

test('technician cannot change another user role', function () {
    $staff = User::factory()->create(['role' => 'hardware_technician']);
    $other = User::factory()->create(['role' => 'hardware_technician']);

    $this->actingAs($staff)->put("/admin/users/{$other->id}/role", ['role' => 'admin'])->assertForbidden();
    expect($other->fresh()->role)->toBe('hardware_technician');
});
