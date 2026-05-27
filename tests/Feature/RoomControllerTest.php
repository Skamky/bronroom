<?php

use App\Models\Office;
use App\Models\Room;
use App\Models\User;

test('can list rooms for an office', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room1 = Room::factory()->forOffice($office)->create();
    $room2 = Room::factory()->forOffice($office)->create();

    $response = $this->actingAs($user)->getJson("/offices/{$office->id}/rooms");

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('unauthenticated user cannot list rooms', function () {
    $office = Office::factory()->create();

    $response = $this->getJson("/offices/{$office->id}/rooms");

    $response->assertStatus(401);
});

test('can show room', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $response = $this->actingAs($user)->getJson("/offices/{$office->id}/rooms/{$room->id}");

    $response->assertStatus(200);
});

test('unauthenticated user cannot access room', function () {
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $response = $this->getJson("/offices/{$office->id}/rooms/{$room->id}");

    $response->assertStatus(401);
});

test('cannot access rooms of another office', function () {
    $user = User::factory()->create();
    $office1 = Office::factory()->create();
    $office2 = Office::factory()->create();
    $room = Room::factory()->forOffice($office1)->create();

    $response = $this->actingAs($user)->get("/offices/{$office2->id}/rooms/{$room->id}");

    $response->assertRedirect("/offices/{$office1->id}/rooms/{$room->id}");
});
