<?php

use App\Models\User;

test('guest is redirected to login', function () {
    $this->get('/')->assertRedirect('/login');
});

test('admin can access dashboard', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/')
        ->assertStatus(200);
});

test('user can access dashboard', function () {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_USER]))
        ->get('/')
        ->assertStatus(200);
});

test('auditor can access dashboard', function () {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_AUDITOR]))
        ->get('/')
        ->assertStatus(200);
});

test('api user cannot access dashboard', function () {
    $this->actingAs(User::factory()->apiUser()->create())
        ->get('/')
        ->assertStatus(403);
});

test('/home redirects to same view', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/home')
        ->assertStatus(200);
});
