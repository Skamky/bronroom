<?php

use App\Models\Event;
use App\Models\Office;
use App\Models\Raspisanie;
use App\Models\Room;
use App\Models\User;

test('can create event with valid data', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $eventData = [
        'title' => 'Test Event',
        'description' => 'Test description',
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $this->assertDatabaseHas('events', ['name' => 'Test Event', 'user_id' => $user->id]);
    $response->assertRedirect();
});

test('cannot create event with invalid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/events', [
        'room_id' => null,
        'title' => '',
        'start_date' => '',
        'end_date' => '',
    ]);

    $response->assertSessionHasErrors(['room_id', 'start_date', 'end_date']);
});

test('cannot create event with start_date after end_date', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $eventData = [
        'title' => 'Invalid Event',
        'start_date' => '2025-03-10 14:00:00',
        'end_date' => '2025-03-10 10:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertSessionHasErrors(['start_date']);
});

test('can update event', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Original Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $updateData = [
        'title' => 'Updated Event',
        'description' => 'Updated description',
        'start_date' => '2025-03-10 14:00:00',
        'end_date' => '2025-03-10 16:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->put("/events/{$event->id}", $updateData);

    $this->assertDatabaseHas('events', ['id' => $event->id, 'name' => 'Updated Event']);
    $response->assertRedirect();
});

test('cannot update event belonging to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user1->id,
        'name' => 'User1 Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $updateData = [
        'title' => 'Hacked Event',
        'start_date' => '2025-03-10 14:00:00',
        'end_date' => '2025-03-10 16:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user2)->put("/events/{$event->id}", $updateData);

    $this->assertDatabaseHas('events', ['id' => $event->id, 'name' => 'User1 Event']);
});

test('can delete own event', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'My Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response = $this->actingAs($user)->delete("/events/{$event->id}");

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
    $response->assertRedirect('/offices');
});

test('cannot delete event belonging to another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user1->id,
        'name' => 'User1 Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response = $this->actingAs($user2)->delete("/events/{$event->id}");

    $this->assertDatabaseHas('events', ['id' => $event->id]);
});

test('can get event data as JSON', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Event',
        'description' => 'Test description',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response = $this->actingAs($user)->getJson("/events/{$event->id}/data");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $event->id,
            'title' => 'Test Event',
            'description' => 'Test description',
        ]);
});

test('unauthenticated user cannot create event', function () {
    $response = $this->post('/events', [
        'title' => 'Test Event',
        'room_id' => 1,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response->assertStatus(302); // Redirect to login
});

test('unauthenticated user cannot update event', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response = $this->put("/events/{$event->id}", [
        'title' => 'Hacked Event',
    ]);

    $response->assertStatus(302); // Redirect to login
});

test('unauthenticated user cannot delete event', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response = $this->delete("/events/{$event->id}");

    $response->assertStatus(302); // Redirect to login
});

test('event edit page returns 200', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response = $this->actingAs($user)->get("/offices/{$office->id}/rooms/{$room->id}/events/{$event->id}/edit");

    $response->assertStatus(200);
});

test('redirect edit returns correct redirect', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $response = $this->actingAs($user)->get("/events/{$event->id}/edit");

    $response->assertRedirect("/offices/{$office->id}/rooms/{$room->id}/events/{$event->id}/edit");
});

test('cannot access event edit for non-existent event', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/events/99999/edit');

    $response->assertStatus(404);
});

test('cannot create event with non-existent room', function () {
    $user = User::factory()->create();

    $eventData = [
        'title' => 'Test Event',
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => 99999,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertSessionHasErrors('room_id');
});

test('cannot update event with non-existent room', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Event',
    ]);

    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $updateData = [
        'title' => 'Updated Event',
        'start_date' => '2025-03-10 14:00:00',
        'end_date' => '2025-03-10 16:00:00',
        'room_id' => 99999,
    ];

    $response = $this->actingAs($user)->put("/events/{$event->id}", $updateData);

    $response->assertSessionHasErrors('room_id');
});

test('get event data returns 404 for non-existent event', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/events/99999/data');

    $response->assertStatus(404);
});

test('cannot update non-existent event', function () {
    $user = User::factory()->create();

    $updateData = [
        'title' => 'Updated Event',
        'start_date' => '2025-03-10 14:00:00',
        'end_date' => '2025-03-10 16:00:00',
        'room_id' => 1,
    ];

    $response = $this->actingAs($user)->put('/events/99999', $updateData);

    $response->assertStatus(404);
});

test('cannot delete non-existent event', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete('/events/99999');

    $response->assertStatus(404);
});

test('can create event without description', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $eventData = [
        'title' => 'Event Without Description',
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertRedirect();
    $this->assertDatabaseHas('events', ['name' => 'Event Without Description', 'user_id' => $user->id]);
});

test('can create event without title and description', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $eventData = [
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertRedirect();
    $this->assertDatabaseHas('events', ['name' => null, 'user_id' => $user->id]);
});

test('can create event with null title and null description', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $eventData = [
        'title' => null,
        'description' => null,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertRedirect();
    $this->assertDatabaseHas('events', ['name' => null, 'user_id' => $user->id]);
});
