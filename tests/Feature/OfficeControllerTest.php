<?php

use App\Models\Office;
use App\Models\User;

test('office index returns 200 for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/offices');

    $response->assertStatus(200);
});

test('office index redirects unauthenticated users', function () {
    $response = $this->get('/offices');

    $response->assertStatus(302);
});

test('can show office', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();

    $response = $this->actingAs($user)->get("/offices/{$office->id}");

    $response->assertStatus(200);
});

test('business hours api returns correct data', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/v1/offices/{$office->id}/businessHours");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'daysOfWeek',
            'startTime',
            'endTime',
        ]);
});

test('business hours api returns 404 for non-existent office', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/offices/99999/businessHours');

    $response->assertStatus(404);
});

test('root route redirects to offices for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect('/offices');
});

test('unauthenticated user redirected from root', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
