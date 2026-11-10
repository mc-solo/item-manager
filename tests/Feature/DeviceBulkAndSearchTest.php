<?php

use App\Models\Device;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('advanced search finds devices by IMEI, serial number and issue', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $byImei = Device::factory()->create(['imei' => '123456789012345', 'serial_number' => 'SN-111', 'issue_description' => 'screen broken']);
    $other = Device::factory()->create(['imei' => '999999999999999', 'serial_number' => 'SN-222', 'issue_description' => 'battery dead']);

    $response = $this->actingAs($user)->getJson('/devices/index?search=123456789012345');
    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($byImei->id)->not->toContain($other->id);

    $response = $this->actingAs($user)->getJson('/devices/index?search=battery dead');
    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($other->id)->not->toContain($byImei->id);
});

test('advanced filters combine device type, amount range and sorting', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $cheap = Device::factory()->create(['device_type' => 'laptop', 'amount' => 500]);
    $expensive = Device::factory()->create(['device_type' => 'laptop', 'amount' => 4000]);
    $mobile = Device::factory()->create(['device_type' => 'mobile', 'amount' => 3000]);

    $response = $this->actingAs($user)->getJson('/devices/index?device_type=laptop&amount_min=1000&amount_max=5000&sort_by=amount&sort_dir=asc');

    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toBe([$expensive->id]);
    expect($ids)->not->toContain($cheap->id, $mobile->id);
});

test('bulk status change works for staff, bulk delete is admin only', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $admin = User::factory()->create(['role' => 'admin']);
    $devices = Device::factory()->count(3)->create(['repair_status' => 'pending']);
    $ids = $devices->pluck('id')->all();

    $this->actingAs($staff)->postJson('/devices/bulk', ['ids' => $ids, 'action' => 'mark_completed'])->assertOk();
    expect(Device::whereIn('id', $ids)->where('repair_status', 'completed')->count())->toBe(3);

    $this->actingAs($staff)->postJson('/devices/bulk', ['ids' => $ids, 'action' => 'delete'])->assertForbidden();
    expect(Device::whereIn('id', $ids)->count())->toBe(3);

    $this->actingAs($admin)->postJson('/devices/bulk', ['ids' => $ids, 'action' => 'delete'])->assertOk();
    expect(Device::whereIn('id', $ids)->count())->toBe(0);
});

test('bulk action rejects an empty selection and unknown actions', function () {
    $user = User::factory()->create(['role' => 'staff']);

    $this->actingAs($user)->postJson('/devices/bulk', ['ids' => [], 'action' => 'mark_completed'])->assertStatus(422);
    $this->actingAs($user)->postJson('/devices/bulk', ['ids' => [1], 'action' => 'explode'])->assertStatus(422);
});

test('export returns the filtered list as CSV', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $device = Device::factory()->create(['repair_status' => 'completed']);
    Device::factory()->create(['repair_status' => 'pending']);

    $response = $this->actingAs($user)->get('/devices/export?status=completed');

    $response->assertOk();
    expect($response->streamedContent())->toContain($device->order_number);
});
