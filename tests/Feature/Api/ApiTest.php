<?php

use App\Models\Device;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

function validDevicePayload(array $overrides = []): array
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

test('login with username returns a token', function () {
    $user = User::factory()->create(['role' => 'admin', 'username' => 'miki']);

    $this->postJson('/api/login', ['login' => 'miki', 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['user' => ['id', 'name', 'username', 'email', 'role'], 'token'])
        ->assertJsonPath('user.role', 'admin');
});

test('login with email returns a token', function () {
    $user = User::factory()->create(['role' => 'reception', 'email' => 'mesi@example.com']);

    $this->postJson('/api/login', ['login' => 'mesi@example.com', 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['user', 'token']);
});

test('login with a wrong password returns 401', function () {
    User::factory()->create(['role' => 'admin', 'username' => 'miki']);

    $this->postJson('/api/login', ['login' => 'miki', 'password' => 'wrong-password'])
        ->assertUnauthorized();
});

test('unauthenticated api devices request returns 401', function () {
    $this->getJson('/api/devices')->assertUnauthorized();
});

test('admin sees all orders, reception sees all orders', function () {
    $tech = User::factory()->create(['role' => 'hardware_technician']);
    Device::factory()->create(['assigned_to' => $tech->id]);
    Device::factory()->create(['assigned_to' => null]);

    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
    $this->getJson('/api/devices')->assertOk()->assertJsonCount(2, 'data');

    Sanctum::actingAs(User::factory()->create(['role' => 'reception']));
    $this->getJson('/api/devices')->assertOk()->assertJsonCount(2, 'data');
});

test('technician sees only orders assigned to them', function () {
    $tech = User::factory()->create(['role' => 'hardware_technician']);
    $other = User::factory()->create(['role' => 'software_technician']);
    Device::factory()->create(['assigned_to' => $tech->id]);
    Device::factory()->create(['assigned_to' => $other->id]);
    Device::factory()->create(['assigned_to' => null]);

    Sanctum::actingAs($tech);

    $this->getJson('/api/devices')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.assigned_to', $tech->id);
});

test('technician cannot view an unassigned order', function () {
    $tech = User::factory()->create(['role' => 'hardware_technician']);
    $device = Device::factory()->create(['assigned_to' => null]);

    Sanctum::actingAs($tech);

    $this->getJson("/api/devices/{$device->id}")->assertForbidden();
});

test('technician cannot delete a device, admin can', function () {
    $tech = User::factory()->create(['role' => 'hardware_technician']);
    $device = Device::factory()->create(['assigned_to' => $tech->id]);

    Sanctum::actingAs($tech);
    $this->deleteJson("/api/devices/{$device->id}")->assertForbidden();
    expect(Device::find($device->id))->not->toBeNull();

    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
    $this->deleteJson("/api/devices/{$device->id}")->assertOk();
    expect(Device::find($device->id))->toBeNull();
});

test('reception cannot complete or update an order', function () {
    $device = Device::factory()->create(['repair_status' => 'pending']);

    Sanctum::actingAs(User::factory()->create(['role' => 'reception']));

    $this->postJson("/api/devices/{$device->id}/complete")->assertForbidden();
    $this->putJson("/api/devices/{$device->id}", validDevicePayload(['repair_status' => 'in_progress']))->assertForbidden();
    expect($device->fresh()->repair_status)->toBe('pending');
});

test('reception can create and assign an order', function () {
    $tech = User::factory()->create(['role' => 'hardware_technician']);

    Sanctum::actingAs(User::factory()->create(['role' => 'reception']));

    $created = $this->postJson('/api/devices', validDevicePayload())
        ->assertCreated()
        ->assertJsonPath('data.client_name', 'Miki');

    $deviceId = $created->json('data.id');
    expect($created->json('data.order_number'))->toStartWith('TPB-');

    $this->postJson("/api/devices/{$deviceId}/assign", [
        'assigned_to' => $tech->id,
        'assign_role' => 'hardware_technician',
    ])->assertOk()->assertJsonPath('data.assigned_to', $tech->id);
});

test('technician can create an order, same as the web', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'hardware_technician']));

    $this->postJson('/api/devices', validDevicePayload())
        ->assertCreated()
        ->assertJsonPath('data.client_name', 'Miki');
});

test('technician can complete their own assigned order', function () {
    $tech = User::factory()->create(['role' => 'hardware_technician']);
    $device = Device::factory()->create(['repair_status' => 'pending', 'assigned_to' => $tech->id]);

    Sanctum::actingAs($tech);

    $this->postJson("/api/devices/{$device->id}/complete")->assertOk();
    expect($device->fresh()->repair_status)->toBe('completed');
});

test('technician can move their own assigned order to in progress', function () {
    $tech = User::factory()->create(['role' => 'hardware_technician']);
    $device = Device::factory()->create(['repair_status' => 'pending', 'assigned_to' => $tech->id]);

    Sanctum::actingAs($tech);

    $this->patchJson("/api/devices/{$device->id}/status", ['repair_status' => 'in_progress'])
        ->assertOk()
        ->assertJsonPath('data.repair_status', 'in_progress');
});

test('create with a bad client number returns 422', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $this->postJson('/api/devices', validDevicePayload(['client_number' => '0123456789']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('client_number');
});

test('search filter finds the matching order only', function () {
    Device::factory()->create(['client_name' => 'Searchable Name']);
    Device::factory()->create(['client_name' => 'Someone Else']);

    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $this->getJson('/api/devices?search=Searchable')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.client_name', 'Searchable Name');
});

test('dashboard returns counts for the signed in user', function () {
    Device::factory()->create(['repair_status' => 'pending']);
    Device::factory()->create(['repair_status' => 'completed', 'amount' => 500]);

    Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

    $this->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('totalDevices', 2)
        ->assertJsonPath('pendingCount', 1)
        ->assertJsonPath('completedCount', 1)
        ->assertJsonPath('totalCompletedAmount', 500)
        ->assertJsonStructure(['statusCounts', 'weekDays', 'amountByType', 'recentDevices', 'needsAttention', 'totalUsers']);
});
