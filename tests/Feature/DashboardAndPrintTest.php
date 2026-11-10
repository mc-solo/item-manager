<?php

use App\Models\Device;
use App\Models\Transaction;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('dashboard shows real counts and amounts, not hardcoded numbers', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Device::factory()->create(['repair_status' => 'completed', 'amount' => 2000]);
    Device::factory()->create(['repair_status' => 'pending', 'amount' => 500]);
    Device::factory()->create(['repair_status' => 'pending', 'amount' => 300]);

    $response = $this->actingAs($admin)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('2,000.00');
    $response->assertSee('Recent orders');
    $response->assertDontSee('Consulting Project A');
    $response->assertDontSee('FRP [USB]');
});

test('dashboard shows the admin users section only to admins', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($admin)->get('/dashboard')->assertSee('Users (admin only)');
    $this->actingAs($staff)->get('/dashboard')->assertDontSee('Users (admin only)');
});

test('dashboard shows recorded payments', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $device = Device::factory()->create();
    Transaction::create(['device_id' => $device->id, 'type' => 'deposit', 'amount' => 1000]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('1 transactions');
    $response->assertSee('1,000.00');
});

test('any logged in user can print an order, guests cannot', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $device = Device::factory()->create(['client_name' => 'Miki Test', 'amount' => 1500]);

    $this->get("/devices/print/{$device->id}")->assertRedirect('/login');

    $response = $this->actingAs($staff)->get("/devices/print/{$device->id}");

    $response->assertOk();
    $response->assertSee($device->order_number);
    $response->assertSee('Miki Test');
    $response->assertSee('1,500.00');
    $response->assertSee('Print this order');
    $response->assertSee('Please bring this receipt');
});
