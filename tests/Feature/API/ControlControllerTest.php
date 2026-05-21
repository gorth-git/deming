<?php

uses()->group('api');

use App\Models\Control;
use App\Models\Measure;
use App\Models\User;
use Laravel\Passport\Passport;

beforeEach(function () {
    $this->apiUser = User::factory()->apiUser()->create();
    Passport::actingAs($this->apiUser);
});

test('index returns all controls', function () {
    Control::factory()->count(3)->create();

    $response = $this->getJson('/api/controls');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('index is forbidden for non-api users', function () {
    Passport::actingAs(User::factory()->admin()->create());

    $response = $this->getJson('/api/controls');

    $response->assertStatus(403);
});

test('store creates a control', function () {
    $data = [
        'name' => 'Access Review',
        'objective' => 'Review access rights quarterly',
        'periodicity' => 3,
        'plan_date' => '2026-06-01',
    ];

    $response = $this->postJson('/api/controls', $data);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Access Review']);

    $this->assertDatabaseHas('controls', ['name' => 'Access Review']);
});

test('store syncs measures when provided', function () {
    $measure = Measure::factory()->create();

    $response = $this->postJson('/api/controls', [
        'name' => 'Control with Measure',
        'objective' => 'Test',
        'plan_date' => '2026-06-01',
        'measures' => [$measure->id],
    ]);

    $response->assertStatus(201);

    $control = Control::where('name', 'Control with Measure')->first();
    expect($control->measures()->count())->toBe(1);
});

test('show returns a single control with measures', function () {
    $control = Control::factory()->create();

    $response = $this->getJson("/api/controls/{$control->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['id' => $control->id])
        ->assertJsonStructure(['measures']);
});

test('update modifies a control', function () {
    $control = Control::factory()->create();

    $response = $this->putJson("/api/controls/{$control->id}", [
        'name' => 'Updated Control',
        'objective' => 'Updated objective',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('controls', ['id' => $control->id, 'name' => 'Updated Control']);
});

test('destroy deletes a control and detaches measures', function () {
    $measure = Measure::factory()->create();
    $control = Control::factory()->create();
    $control->measures()->attach($measure->id);

    $response = $this->deleteJson("/api/controls/{$control->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('controls', ['id' => $control->id]);
    $this->assertDatabaseMissing('control_measure', ['control_id' => $control->id]);
});

test('show returns 404 for nonexistent control', function () {
    $response = $this->getJson('/api/controls/9999');

    $response->assertStatus(404);
});
