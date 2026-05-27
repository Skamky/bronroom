<?php

use App\Models\User;

test('unauthenticated user cannot access api v1 user endpoint', function () {
    $response = $this->getJson('/api/v1/user');

    $response->assertStatus(401);
});

test('authenticated user can access api v1 user endpoint', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/user');

    $response->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

test('api v1 user endpoint returns correct user data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/user');

    $response->assertStatus(200);

    $responseData = $response->json();
    expect($responseData['id'])->toBe($user->id);
    expect($responseData['name'])->toBe($user->name);
    expect($responseData['email'])->toBe($user->email);
});

test('different users get their own data from api v1 user endpoint', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $response1 = $this->actingAs($user1)->getJson('/api/v1/user');
    $response2 = $this->actingAs($user2)->getJson('/api/v1/user');

    $response1->assertStatus(200)
        ->assertJson(['id' => $user1->id]);

    $response2->assertStatus(200)
        ->assertJson(['id' => $user2->id]);

    expect($response1->json()['id'])->not()->toBe($user2->id);
    expect($response2->json()['id'])->not()->toBe($user1->id);
});
