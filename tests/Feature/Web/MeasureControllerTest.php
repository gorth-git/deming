<?php

use App\Models\Domain;
use App\Models\Measure;
use App\Models\User;

beforeEach(function () {
    $this->admin   = User::factory()->admin()->create();
    $this->user    = User::factory()->create(['role' => User::ROLE_USER]);
    $this->auditor = User::factory()->auditor()->create();
});

test('guest is redirected to login', function () {
    $this->get('/alice/index')->assertRedirect('/login');
});

test('admin can list measures', function () {
    Measure::factory()->count(3)->create();
    $this->actingAs($this->admin)->get('/alice/index')->assertStatus(200);
});

test('auditor can list measures', function () {
    $this->actingAs($this->auditor)->get('/alice/index')->assertStatus(200);
});

test('api user cannot list measures', function () {
    $this->actingAs(User::factory()->apiUser()->create())
        ->get('/alice/index')
        ->assertStatus(403);
});

test('admin can access create form', function () {
    $this->actingAs($this->admin)->get('/alice/create')->assertStatus(200);
});

test('auditor cannot access create form', function () {
    $this->actingAs($this->auditor)->get('/alice/create')->assertStatus(403);
});

test('admin can create a measure', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->admin)
        ->post('/alice/store', [
            'domain_id' => $domain->id,
            'clause' => '5.1.1',
            'name' => 'Access Control Policy',
            'objective' => 'Ensure access is controlled properly',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('measures', ['clause' => '5.1.1']);
});

test('non-admin user can create a measure', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->post('/alice/store', [
            'domain_id' => $domain->id,
            'clause' => '5.1.2',
            'name' => 'Mobile Device Policy',
            'objective' => 'Ensure mobile device access is controlled',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('measures', ['clause' => '5.1.2']);
});

test('auditor cannot create a measure', function () {
    $domain = Domain::factory()->create();
    $this->actingAs($this->auditor)
        ->post('/alice/store', [
            'domain_id' => $domain->id,
            'clause' => '5.1.3',
            'name' => 'Policy',
            'objective' => 'Objective',
        ])
        ->assertStatus(403);
});

test('admin can view a measure', function () {
    $measure = Measure::factory()->create();
    $this->actingAs($this->admin)->get("/alice/show/{$measure->id}")->assertStatus(200);
});

test('admin can edit a measure', function () {
    $measure = Measure::factory()->create();
    $this->actingAs($this->admin)->get("/alice/{$measure->id}/edit")->assertStatus(200);
});

test('auditor cannot edit a measure', function () {
    $measure = Measure::factory()->create();
    $this->actingAs($this->auditor)->get("/alice/{$measure->id}/edit")->assertStatus(403);
});

test('admin can update a measure', function () {
    $measure = Measure::factory()->create();

    $this->actingAs($this->admin)
        ->post("/alice/save/{$measure->id}", [
            'domain_id' => $measure->domain_id,
            'clause' => $measure->clause,
            'name' => 'Updated Measure Name',
            'objective' => 'Updated objective text here',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('measures', ['id' => $measure->id, 'name' => 'Updated Measure Name']);
});

test('admin can delete a measure', function () {
    $measure = Measure::factory()->create();

    $this->actingAs($this->admin)
        ->post("/alice/delete/{$measure->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('measures', ['id' => $measure->id]);
});

test('auditor cannot delete a measure', function () {
    $measure = Measure::factory()->create();
    $this->actingAs($this->auditor)
        ->post("/alice/delete/{$measure->id}")
        ->assertStatus(403);
});

test('admin can export measures', function () {
    Measure::factory()->count(2)->create();
    $this->actingAs($this->admin)->get('/export/alices')->assertStatus(200);
});
