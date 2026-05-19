<?php

use App\Models\Domain;
use App\Models\Measure;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('guest is redirected to login', function () {
    $this->get('/global-search?q=test')->assertRedirect('/login');
});

test('admin can perform a global search', function () {
    Domain::factory()->create(['title' => 'Security Policies']);
    Measure::factory()->create(['name' => 'Access Control Policy']);

    // GlobalSearchController uses 'search' parameter, not 'q'
    $this->actingAs($this->admin)
        ->get('/global-search?search=policy')
        ->assertStatus(200);
});

test('api user cannot perform global search', function () {
    $this->actingAs(User::factory()->apiUser()->create())
        ->get('/global-search?search=test')
        ->assertStatus(403);
});

test('search with no query redirects back', function () {
    // Without 'search' param, controller redirects back
    $this->actingAs($this->admin)
        ->get('/global-search')
        ->assertRedirect();
});
