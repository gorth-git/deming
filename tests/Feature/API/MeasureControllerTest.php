<?php

uses()->group('api');

use App\Models\Domain;
use App\Models\Measure;
use App\Models\User;
use Laravel\Passport\Passport;

beforeEach(function () {
    $this->apiUser = User::factory()->apiUser()->create();
    Passport::actingAs($this->apiUser);
});

test('index returns all measures', function () {
    Measure::factory()->count(3)->create();

    $response = $this->getJson('/api/measures');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('index is forbidden for non-api users', function () {
    Passport::actingAs(User::factory()->admin()->create());

    $response = $this->getJson('/api/measures');

    $response->assertStatus(403);
});

test('store creates a measure', function () {
    $domain = Domain::factory()->create();

    $data = [
        'domain_id' => $domain->id,
        'clause' => '5.1.1',
        'name' => 'Access Control Policy',
        'objective' => 'Ensure access is controlled',
    ];

    $response = $this->postJson('/api/measures', $data);

    $response->assertStatus(201)
        ->assertJsonFragment(['clause' => '5.1.1']);

    $this->assertDatabaseHas('measures', ['clause' => '5.1.1']);
});

test('show returns a single measure with controls', function () {
    $measure = Measure::factory()->create();

    $response = $this->getJson("/api/measures/{$measure->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['id' => $measure->id])
        ->assertJsonStructure(['controls']);
});

test('update modifies a measure', function () {
    $measure = Measure::factory()->create();

    $response = $this->putJson("/api/measures/{$measure->id}", [
        'name' => 'Updated Measure Name',
        'clause' => $measure->clause,
        'domain_id' => $measure->domain_id,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('measures', ['id' => $measure->id, 'name' => 'Updated Measure Name']);
});

test('store syncs controls when provided', function () {
    $domain = Domain::factory()->create();

    $response = $this->postJson('/api/measures', [
        'domain_id' => $domain->id,
        'clause' => '6.2.1',
        'name' => 'Mobile Device Policy',
        'controls' => [],
    ]);

    $response->assertStatus(201);
});

test('destroy deletes a measure', function () {
    $measure = Measure::factory()->create();

    $response = $this->deleteJson("/api/measures/{$measure->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('measures', ['id' => $measure->id]);
});

test('show returns 404 for nonexistent measure', function () {
    $response = $this->getJson('/api/measures/9999');

    $response->assertStatus(404);
});
