<?php

use App\Models\Device;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function validOrderPayload(array $overrides = []): array
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

test('admin can create a hardware technician and a software technician, the old staff role is rejected', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Miki',
        'username' => 'miki',
        'email' => 'miki@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'hardware_technician',
    ])->assertRedirect();
    expect(User::where('username', 'miki')->first()->role)->toBe('hardware_technician');

    $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Mesi',
        'username' => 'mesi',
        'email' => 'mesi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'software_technician',
    ])->assertRedirect();
    expect(User::where('username', 'mesi')->first()->role)->toBe('software_technician');

    $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Nope',
        'username' => 'nope',
        'email' => 'nope@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'staff',
    ])->assertSessionHasErrors('role');
});

test('an order can be assigned to a hardware technician, but not to a software technician as hardware', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $hardware = User::factory()->create(['role' => 'hardware_technician', 'username' => 'miki']);
    $software = User::factory()->create(['role' => 'software_technician', 'username' => 'mesi']);

    $this->actingAs($admin)->postJson('/devices/store', validOrderPayload([
        'assign_role' => 'hardware_technician',
        'assigned_to' => $hardware->id,
    ]))->assertOk();
    expect(Device::first()->assigned_to)->toBe($hardware->id);

    $this->actingAs($admin)->postJson('/devices/store', validOrderPayload([
        'assign_role' => 'hardware_technician',
        'assigned_to' => $software->id,
    ]))->assertStatus(422);
});

test('an order can be created unassigned when no technician of that type exists yet', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->postJson('/devices/store', validOrderPayload([
        'assign_role' => 'hardware_technician',
        'assigned_to' => null,
    ]))->assertOk();
    expect(Device::first()->assigned_to)->toBeNull();
});

test('an order must be assigned when a technician of that type exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['role' => 'hardware_technician', 'username' => 'miki']);

    $this->actingAs($admin)->postJson('/devices/store', validOrderPayload([
        'assign_role' => 'hardware_technician',
        'assigned_to' => null,
    ]))->assertStatus(422);
});

test('a technician only sees their own assigned orders, an admin sees all', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $hardware = User::factory()->create(['role' => 'hardware_technician', 'username' => 'miki']);
    $software = User::factory()->create(['role' => 'software_technician', 'username' => 'mesi']);
    $mine = Device::factory()->create(['assigned_to' => $hardware->id]);
    $theirs = Device::factory()->create(['assigned_to' => $software->id]);
    $unassigned = Device::factory()->create(['assigned_to' => null]);

    $response = $this->actingAs($hardware)->getJson('/devices/index');
    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($mine->id)->not->toContain($theirs->id, $unassigned->id);

    $this->actingAs($hardware)->get("/devices/show/{$theirs->id}")->assertForbidden();
    $this->actingAs($hardware)->get("/devices/show/{$unassigned->id}")->assertForbidden();
    $this->actingAs($hardware)->get("/devices/show/{$mine->id}")->assertOk();

    $response = $this->actingAs($admin)->getJson('/devices/index');
    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($mine->id, $theirs->id, $unassigned->id);
});

test('a technician cannot complete or bulk change an order assigned to someone else', function () {
    $hardware = User::factory()->create(['role' => 'hardware_technician', 'username' => 'miki']);
    $software = User::factory()->create(['role' => 'software_technician', 'username' => 'mesi']);
    $device = Device::factory()->create(['repair_status' => 'pending', 'assigned_to' => $software->id]);

    $this->actingAs($hardware)->post("/devices/{$device->id}/complete")->assertForbidden();
    expect($device->fresh()->repair_status)->toBe('pending');

    $this->actingAs($hardware)->postJson('/devices/bulk', ['ids' => [$device->id], 'action' => 'mark_completed'])->assertForbidden();
    expect($device->fresh()->repair_status)->toBe('pending');
});
