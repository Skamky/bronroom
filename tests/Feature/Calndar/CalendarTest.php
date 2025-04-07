<?php


use App\Models\Office;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\Helpers\EventHelper;

$url = '/calendar';

test('no authorize', function () use ($url) {
    $this->getJson($url)->assertStatus(401);
    $this->postJson($url)->assertStatus(401);
    $this->putJson($url)->assertStatus(401);
    $this->deleteJson($url)->assertStatus(401);
    $this->patchJson($url)->assertStatus(401);
    $this->optionsJson($url)->assertStatus(401);
});

test('authorize no body', function () use ($url) {

    $user = User::factory()->create();

    $this->actingAs($user)->getJson($url)->assertStatus(422);
    $this->actingAs($user)->postJson($url)->assertStatus(422);
});

test('no rooms', function () use ($url) {
    $user = User::factory()->create();

    $start = '2025-03-09T00:00:00+05:00';
    $end = '2025-03-10T00:00:00+05:00';

    $response = $this->actingAs($user)->postJson($url, [
        'start' => $start,
        'end' => $end,
        'rooms' => ['2']
    ]);
    $response->assertStatus(422);

});


test('no events', function () use ($url) {
    $user = User::factory()->create();
    $room = Room::factory()->forOffice(Office::factory()->create())->create();

    $start = '2025-03-09T00:00:00+05:00';
    $end = '2025-03-10T00:00:00+05:00';

    $response = $this->actingAs($user)->postJson($url, [
        'start' => $start,
        'end' => $end,
        'rooms' => [$room->id]
    ]);

    $response->assertStatus(200)->assertExactJson([])->assertJson([])->assertJsonStructure([]);
});

it('with event in different intervals', function ($start, $end, $eventStart, $eventEnd) use ($url) {
    $user = User::factory()->create();
    $room = Room::factory()->forOffice(Office::factory()->create())->create();

    $event = EventHelper::create($user, $room, $eventStart, $eventEnd);

    /** @var TestResponse $response */
    $response = $this->actingAs($user)->postJson($url, [
        'start' => $start,
        'end' => $end,
        'rooms' => [$room->id]
    ]);

    $response->assertStatus(200)->assertJson([
        [
            'title' => $event->name,
            'start' => $eventStart->toIso8601String(),
            'end' => $eventEnd->setTimezone('UTC')->toIso8601String(),
        ]
    ]);
})->with([
    'with event out interval up && down' => [
        '2025-03-09T00:00:00+00:00',
        '2025-03-10T00:00:00+00:00',
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-08T00:01:00+00:00'),
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-10T00:02:00+00:00'),
    ],
    'with event start in interval end out' => [
        '2025-03-09T00:00:00+00:00',
        '2025-03-10T00:00:00+00:00',
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T00:01:00+00:00'),
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-10T00:02:00+00:00'),
    ],
    'with event start out interval end in' => [
        '2025-03-09T00:00:00+00:00',
        '2025-03-10T00:00:00+00:00',
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-08T00:01:00+00:00'),
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T00:02:00+00:00'),
        
    ],
]);

test('multiple rooms with events', function () use ($url) {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    
    // Create two rooms in the same office
    $room1 = Room::factory()->forOffice($office)->create();
    $room2 = Room::factory()->forOffice($office)->create();
    
    // Create events in different rooms
    $start = Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T00:00:00+00:00');
    $end = Carbon::createFromFormat(Carbon::ISO8601, '2025-03-10T00:00:00+00:00');
    
    $event1 = EventHelper::create($user, $room1, 
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T10:00:00+00:00'),
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T12:00:00+00:00')
    );
    
    $event2 = EventHelper::create($user, $room2,
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T14:00:00+00:00'),
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T16:00:00+00:00')
    );

    $response = $this->actingAs($user)->postJson($url, [
        'start' => $start->toIso8601String(),
        'end' => $end->toIso8601String(),
        'rooms' => [$room1->id, $room2->id]
    ]);

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJson([
            [
                'title' => $event1->name,
                'start' => '2025-03-09T10:00:00+00:00',
                'end' => '2025-03-09T12:00:00+00:00',
            ],
            [
                'title' => $event2->name,
                'start' => '2025-03-09T14:00:00+00:00',
                'end' => '2025-03-09T16:00:00+00:00',
            ]
        ]);
});

test('events from one room are not visible in another room of the same office', function () use ($url) {
    $user = User::factory()->create();
    $office = Office::factory()->create();
    
    // Create two rooms in the same office
    $room1 = Room::factory()->forOffice($office)->create();
    $room2 = Room::factory()->forOffice($office)->create();
    
    // Create an event only in room1
    $start = Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T00:00:00+00:00');
    $end = Carbon::createFromFormat(Carbon::ISO8601, '2025-03-10T00:00:00+00:00');
    
    $event = EventHelper::create($user, $room1, 
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T10:00:00+00:00'),
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T12:00:00+00:00')
    );

    // Request events for room2 only
    $response = $this->actingAs($user)->postJson($url, [
        'start' => $start->toIso8601String(),
        'end' => $end->toIso8601String(),
        'rooms' => [$room2->id]
    ]);

    // Should not see the event from room1
    $response->assertStatus(200)
        ->assertJsonCount(0);
});

test('events from one room are not visible in another room of different office', function () use ($url) {
    $user = User::factory()->create();
    
    // Create two different offices
    $office1 = Office::factory()->create();
    $office2 = Office::factory()->create();
    
    // Create one room in each office
    $room1 = Room::factory()->forOffice($office1)->create();
    $room2 = Room::factory()->forOffice($office2)->create();
    
    // Create an event only in room1
    $start = Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T00:00:00+00:00');
    $end = Carbon::createFromFormat(Carbon::ISO8601, '2025-03-10T00:00:00+00:00');
    
    $event = EventHelper::create($user, $room1, 
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T10:00:00+00:00'),
        Carbon::createFromFormat(Carbon::ISO8601, '2025-03-09T12:00:00+00:00')
    );

    // Request events for room2 only
    $response = $this->actingAs($user)->postJson($url, [
        'start' => $start->toIso8601String(),
        'end' => $end->toIso8601String(),
        'rooms' => [$room2->id]
    ]);

    // Should not see the event from room1
    $response->assertStatus(200)
        ->assertJsonCount(0);
});

