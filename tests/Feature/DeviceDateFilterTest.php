<?php

use App\Models\Device;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('last_week returns the previous week, not the current week', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $lastWeek = Device::factory()->create(['created_at' => now()->subWeek()->startOfWeek()->addDay()]);
    $thisWeek = Device::factory()->create(['created_at' => now()->startOfWeek()->addDay()]);

    $response = $this->actingAs($user)->getJson('/devices/index?date_range=last_week');

    $response->assertOk();
    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($lastWeek->id)->not->toContain($thisWeek->id);
});

test('last_month returns the previous month, not the current month', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $lastMonth = Device::factory()->create(['created_at' => now()->subMonthNoOverflow()->startOfMonth()->addDay()]);
    $thisMonth = Device::factory()->create(['created_at' => now()->startOfMonth()->addDay()]);

    $response = $this->actingAs($user)->getJson('/devices/index?date_range=last_month');

    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($lastMonth->id)->not->toContain($thisMonth->id);
});

test('custom date range filters by from and to dates', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $inside = Device::factory()->create(['created_at' => now()->subDays(5)]);
    $outside = Device::factory()->create(['created_at' => now()->subDays(20)]);

    $from = now()->subDays(7)->format('Y-m-d');
    $to = now()->subDays(3)->format('Y-m-d');
    $response = $this->actingAs($user)->getJson("/devices/index?date_range=custom&date_from={$from}&date_to={$to}");

    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($inside->id)->not->toContain($outside->id);
});

test('this_week and this_month return the current week and month', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $thisWeek = Device::factory()->create(['created_at' => now()->startOfWeek()->addDay()]);
    $lastWeek = Device::factory()->create(['created_at' => now()->subWeek()->startOfWeek()->addDay()]);

    $response = $this->actingAs($user)->getJson('/devices/index?date_range=this_week');

    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toContain($thisWeek->id)->not->toContain($lastWeek->id);
});
