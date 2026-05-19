<?php

use App\Models\User;

test('guest is redirected to login', function () {
    $this->get('/profile')->assertRedirect('/login');
});

test('authenticated user can view their profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/profile')->assertStatus(200);
});

test('any role can access profile', function () {
    foreach ([User::ROLE_ADMIN, User::ROLE_USER, User::ROLE_AUDITOR, User::ROLE_AUDITEE] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user)->get('/profile')->assertStatus(200);
    }
});
