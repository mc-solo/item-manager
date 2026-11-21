<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'login' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can authenticate with their username', function () {
    $user = User::factory()->create(['username' => 'miki']);

    $response = $this->post('/login', [
        'login' => 'miki',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'login' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');

    $this->assertGuest();
});