<?php

use App\Models\Device;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function receptionOrderPayload(array $overrides = []): array
{
    return array_merge([
        'client_name' => 'Miki',
        'client_number' => '0912345678',
        'amount' => 1000,
        'device_type' => 'mobile',
        'device_model' => 'iPhone 13',
        'repair_status' => 'pending',
    ], $overrides);
}

test('admin can create a reception user', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Reception',
        'username' => 'reception',
        'email' => 'reception@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'reception',
    ])->assertRedirect();
    expect(User::where('username', 'reception')->first()->role)->toBe('reception');
});

test('reception can add an order and assign it to a hardware technician, but not to a reception user', function () {
    $reception = User::factory()->create(['role' => 'reception', 'username' => 'reception']);
    $hardware = User::factory()->create(['role' => 'hardware_technician', 'username' => 'miki']);

    $this->actingAs($reception)->postJson('/devices/store', receptionOrderPayload([
        'assign_role' => 'hardware_technician',
        'assigned_to' => $hardware->id,
    ]))->assertOk();
    expect(Device::first()->assigned_to)->toBe($hardware->id);

    $this->actingAs($reception)->postJson('/devices/store', receptionOrderPayload([
        'assign_role' => 'hardware_technician',
        'assigned_to' => $reception->id,
    ]))->assertStatus(422);
});

test('reception sees all orders read-only, including unassigned and other technicians orders', function () {
    $reception = User::factory()->create(['role' => 'reception']);
    $hardware = User::factory()->create(['role' => 'hardware_technician', 'username' => 'miki']);
    $mine = Device::factory()->create(['assigned_to' => $hardware->id]);
    $unassigned = Device::factory()->create(['assigned_to' => null]);

    $response = $this->actingAs($reception)->getJson('/devices/index');
    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($mine->id, $unassigned->id);

    $this->actingAs($reception)->get("/devices/show/{$mine->id}")->assertOk();
    $this->actingAs($reception)->get("/devices/show/{$unassigned->id}")->assertOk();
    $this->actingAs($reception)->get("/devices/print/{$mine->id}")->assertOk();
});

test('reception cannot edit, complete, delete, bulk change or export orders', function () {
    $reception = User::factory()->create(['role' => 'reception']);
    $hardware = User::factory()->create(['role' => 'hardware_technician', 'username' => 'miki']);
    $device = Device::factory()->create(['repair_status' => 'pending', 'assigned_to' => $hardware->id]);

    $this->actingAs($reception)->get("/devices/edit/{$device->id}")->assertForbidden();
    $this->actingAs($reception)->put("/devices/update/{$device->id}", receptionOrderPayload(['client_name' => 'Changed']))->assertForbidden();
    $this->actingAs($reception)->post("/devices/{$device->id}/complete")->assertForbidden();
    expect($device->fresh()->repair_status)->toBe('pending');

    $this->actingAs($reception)->delete("/devices/delete/{$device->id}")->assertForbidden();
    expect(Device::find($device->id))->not->toBeNull();

    $this->actingAs($reception)->postJson('/devices/bulk', ['ids' => [$device->id], 'action' => 'mark_completed'])->assertForbidden();
    expect($device->fresh()->repair_status)->toBe('pending');

    $this->actingAs($reception)->get('/devices/export')->assertForbidden();
});

test('reception cannot open Manage Users', function () {
    $reception = User::factory()->create(['role' => 'reception']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($reception)->get('/admin/users')->assertForbidden();
    $this->actingAs($admin)->get('/admin/users')->assertOk();
});
