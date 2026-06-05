<?php

use App\Models\Event;
use App\Models\Office;
use App\Models\Raspisanie;
use App\Models\Room;
use App\Models\User;

test('cannot create event in occupied room with overlapping time', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    // Create existing event in the same room with overlapping time
    $existingEvent = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Existing Event',
    ]);

    Raspisanie::create([
        'event_id' => $existingEvent->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 11:00:00',
        'end_date' => '2025-03-10 13:00:00',
    ]);

    $eventData = [
        'title' => 'Conflicting Event',
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertSessionHasErrors('overlap');
});

test('cannot create event with same time overlap', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    // Create existing event in the same room
    $existingEvent = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Existing Event',
    ]);

    Raspisanie::create([
        'event_id' => $existingEvent->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
    ]);

    $eventData = [
        'title' => 'Exact Overlap Event',
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertSessionHasErrors('overlap');
});

test('can create event in same room with non-overlapping time', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    // Create existing event in the room in a different time slot
    $existingEvent = Event::factory()->create([
        'user_id' => $user->id,
        'name' => 'Morning Event',
    ]);

    Raspisanie::create([
        'event_id' => $existingEvent->id,
        'room_id' => $room->id,
        'start_date' => '2025-03-10 08:00:00',
        'end_date' => '2025-03-10 09:00:00',
    ]);

    $eventData = [
        'title' => 'Afternoon Event',
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 12:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $this->assertDatabaseHas('events', ['name' => 'Afternoon Event']);
    $response->assertRedirect();
});

test('cannot create event with same start and end date', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    $room = Room::factory()->forOffice($office)->create();

    $eventData = [
        'title' => 'Zero Duration Event',
        'start_date' => '2025-03-10 10:00:00',
        'end_date' => '2025-03-10 10:00:00',
        'room_id' => $room->id,
    ];

    $response = $this->actingAs($user)->post('/events', $eventData);

    $response->assertSessionHasErrors(['start_date', 'end_date']);
});
