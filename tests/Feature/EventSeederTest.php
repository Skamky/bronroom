<?php

use App\Models\Event;
use App\Models\Office;
use App\Models\Raspisanie;
use App\Models\Room;
use App\Models\User;
use App\Services\EventService;
use Database\Seeders\EventSeeder;

beforeEach(function () {
    // Truncate existing data to start fresh
    Raspisanie::query()->delete();
    Event::query()->delete();
    Room::query()->delete();
    Office::query()->delete();
    User::query()->delete();
});

test('event seeder creates events for users and rooms', function () {
    $office = Office::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $seeder = new EventSeeder(app(EventService::class));
    $seeder->run();

    // Verify events were created
    expect(Event::count())->toBeGreaterThan(0);
});

test('event seeder does not create events with same start and end date', function () {
    $office = Office::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $seeder = new EventSeeder(app(EventService::class));
    $seeder->run();

    // Check that no event has same start and end date
    $invalidEvents = Raspisanie::where('start_date', '=', DB::raw('end_date'))->get();
    expect($invalidEvents->count())->toBe(0);
});

test('event seeder respects office working hours', function () {
    $office = Office::factory()->create([
        'start_time' => '10:00:00',
        'end_time' => '16:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $seeder = new EventSeeder(app(EventService::class));
    $seeder->run();

    // Verify all events are within office working hours
    $violations = Raspisanie::whereRaw(
        'STR_TO_DATE(start_date, "%Y-%m-%d %H:%i:%s") < CONCAT(DATE(start_date), " ", ?) OR STR_TO_DATE(end_date, "%Y-%m-%d %H:%i:%s") > CONCAT(DATE(end_date), " ", ?)',
        [$office->start_time->format('H:i:s'), $office->end_time->format('H:i:s')]
    )->get();

    expect($violations->count())->toBe(0);
});

test('event seeder does not create overlapping events in same room', function () {
    $office = Office::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $seeder = new EventSeeder(app(EventService::class));
    $seeder->run();

    // Check for overlapping events in the same room
    $overlaps = Raspisanie::select('r1.room_id', 'r1.event_id as event1', 'r2.event_id as event2')
        ->from('raspisanies as r1')
        ->join('raspisanies as r2', function ($join) {
            $join->on('r1.room_id', '=', 'r2.room_id')
                ->where('r1.event_id', '<', 'r2.event_id');
        })
        ->where(function ($query) {
            $query->whereBetween('r1.start_date', ['r2.start_date', 'r2.end_date'])
                ->orWhereBetween('r1.end_date', ['r2.start_date', 'r2.end_date'])
                ->orWhere(function ($q) {
                    $q->where('r1.start_date', '<', 'r2.start_date')
                        ->where('r1.end_date', '>', 'r2.end_date');
                });
        })
        ->get();

    expect($overlaps->count())->toBe(0);
});

test('event seeder limits events to office working hours with short window', function () {
    // Very short office window to test edge cases
    $office = Office::factory()->create([
        'start_time' => '12:00:00',
        'end_time' => '13:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $seeder = new EventSeeder(app(EventService::class));
    $seeder->run();

    // All events should be within 12:00 - 13:00
    $violations = Raspisanie::whereRaw(
        'STR_TO_DATE(start_date, "%Y-%m-%d %H:%i:%s") < CONCAT(DATE(start_date), " ", ?) OR STR_TO_DATE(end_date, "%Y-%m-%d %H:%i:%s") > CONCAT(DATE(end_date), " ", ?)',
        [$office->start_time->format('H:i:s'), $office->end_time->format('H:i:s')]
    )->get();

    expect($violations->count())->toBe(0);
});

test('event seeder does not create events with end_date before start_date', function () {
    $office = Office::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $seeder = new EventSeeder(app(EventService::class));
    $seeder->run();

    $invalidEvents = Raspisanie::whereColumn('start_date', '>=', 'end_date')->get();

    expect($invalidEvents->count())->toBe(0);
});

test('checkEventOverlap applies whereColumn filter for valid records', function () {
    $office = Office::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $event = Event::factory()->create(['user_id' => $user->id]);
    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2026-06-10 10:00:00',
        'end_date' => '2026-06-10 12:00:00',
    ]);

    $service = app(EventService::class);
    $sql = $service->checkEventOverlap(
        $room,
        '2026-06-10 11:00:00',
        '2026-06-10 13:00:00'
    );

    expect($sql)->toBeTrue();
});

test('checkEventOverlap detects overlap with valid records', function () {
    $office = Office::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $event = Event::factory()->create(['user_id' => $user->id]);
    Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2026-06-10 10:00:00',
        'end_date' => '2026-06-10 12:00:00',
    ]);

    $service = app(EventService::class);
    $hasOverlap = $service->checkEventOverlap(
        $room,
        '2026-06-10 11:00:00',
        '2026-06-10 13:00:00'
    );

    expect($hasOverlap)->toBeTrue();
});

test('CHECK constraint prevents invalid time range', function () {
    $office = Office::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);
    $room = Room::factory()->forOffice($office)->create();
    $user = User::factory()->create();

    $event = Event::factory()->create(['user_id' => $user->id]);

    expect(fn () => Raspisanie::create([
        'event_id' => $event->id,
        'room_id' => $room->id,
        'start_date' => '2026-06-10 12:00:00',
        'end_date' => '2026-06-10 10:00:00',
    ]))->toThrow(PDOException::class);
});
