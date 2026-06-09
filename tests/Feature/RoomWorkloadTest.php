<?php

use App\Models\Event;
use App\Models\Office;
use App\Models\Room;
use App\Models\User;

test('workload calculates correctly with pivot dates', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // Событие 1: 06:00 - 07:00 (60 минут)
    $event1 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event1, [
        'start_date' => $today->copy()->setTime(6, 0),
        'end_date' => $today->copy()->setTime(7, 0),
    ]);

    // Событие 2: 12:15 - 13:45 (90 минут)
    $event2 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event2, [
        'start_date' => $today->copy()->setTime(12, 15),
        'end_date' => $today->copy()->setTime(13, 45),
    ]);

    // Явно подгружаем office, чтобы toArray() мог его использовать
    $room->load('office');
    $serialized = $room->toArray();

    // expandedStart = today 05:30, expandedEnd = today 15:30 = 600 минут всего
    // Занято: 60 мин (06:00-07:00) + 90 мин (12:15-13:45) = 150 минут
    // Workload = 150/600 * 100 = 25%
    expect($serialized['workload'])->toBeGreaterThan(24.0)->toBeLessThan(26.0);
});

test('workload returns 0 when no events', function () {
    $office = Office::factory()->create([
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::factory()->forOffice($office)->create();

    $serialized = $room->toArray();

    expect($serialized['workload'])->toBe(0.0);
});

test('close events with small gaps are merged', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // Событие 1: 09:00 - 09:30 (30 минут)
    $event1 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event1, [
        'start_date' => $today->copy()->setTime(9, 0),
        'end_date' => $today->copy()->setTime(9, 30),
    ]);

    // Событие 2: 09:35 - 10:05 (30 минут, разрыв 5 мин < 10 мин порога)
    $event2 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event2, [
        'start_date' => $today->copy()->setTime(9, 35),
        'end_date' => $today->copy()->setTime(10, 5),
    ]);

    $room->load('office');
    $serialized = $room->toArray();

    // Слияние: 09:00 - 10:05 = 65 минут
    // Workload = 65/600 * 100 = 10.833...%
    expect((float)$serialized['workload'])->toBeGreaterThan(10.8)->toBeLessThan(10.9);
});

test('chain of close events merges into single block', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // A: 09:00 - 09:30
    $eventA = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($eventA, [
        'start_date' => $today->copy()->setTime(9, 0),
        'end_date' => $today->copy()->setTime(9, 30),
    ]);

    // B: 09:35 - 10:05 (разрыв с A = 5 мин → сливаем)
    $eventB = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($eventB, [
        'start_date' => $today->copy()->setTime(9, 35),
        'end_date' => $today->copy()->setTime(10, 5),
    ]);

    // C: 10:10 - 10:40 (разрыв с расширенным блоком A+B = 10:05+5 = 10:10 → сливаем)
    $eventC = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($eventC, [
        'start_date' => $today->copy()->setTime(10, 10),
        'end_date' => $today->copy()->setTime(10, 40),
    ]);

    $room->load('office');
    $serialized = $room->toArray();

    // Итоговый блок: 09:00 - 10:40 = 100 минут
    // Workload = 100/600 * 100 = 16.67%
    expect((float)$serialized['workload'])->toBeGreaterThan(16.6)->toBeLessThan(16.7);
});

test('events exactly at threshold merge', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // Событие 1: 09:00 - 09:30 (30 мин)
    $event1 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event1, [
        'start_date' => $today->copy()->setTime(9, 0),
        'end_date' => $today->copy()->setTime(9, 30),
    ]);

    // Событие 2: 09:40 - 10:00 (20 мин, разрыв ровно 10 мин = порог)
    $event2 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event2, [
        'start_date' => $today->copy()->setTime(9, 40),
        'end_date' => $today->copy()->setTime(10, 0),
    ]);

    $room->load('office');
    $serialized = $room->toArray();

    // Должны слиться: 09:00 - 10:00 = 60 мин => 10%
    expect((float)$serialized['workload'])->toBeGreaterThan(9.99)->toBeLessThan(10.01);
});

test('events just above threshold do not merge', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // Событие 1: 09:00 - 09:30 (30 мин)
    $event1 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event1, [
        'start_date' => $today->copy()->setTime(9, 0),
        'end_date' => $today->copy()->setTime(9, 30),
    ]);

    // Событие 2: 09:41 - 10:00 (19 мин, разрыв 11 мин > порога 10 мин)
    $event2 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event2, [
        'start_date' => $today->copy()->setTime(9, 41),
        'end_date' => $today->copy()->setTime(10, 0),
    ]);

    $room->load('office');
    $serialized = $room->toArray();

    // НЕ сливаются: 30 + 19 = 49 мин => 8.167%
    expect((float)$serialized['workload'])->toBeGreaterThan(8.16)->toBeLessThan(8.18);
});

test('events outside expanded work hours are trimmed', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // Событие начинается до expandedStart (05:30)
    $event1 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event1, [
        'start_date' => $today->copy()->setTime(4, 0),
        'end_date' => $today->copy()->setTime(6, 0),
    ]);

    // Событие заканчивается после expandedEnd (15:30)
    $event2 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event2, [
        'start_date' => $today->copy()->setTime(15, 0),
        'end_date' => $today->copy()->setTime(17, 0),
    ]);

    $room->load('office');
    $serialized = $room->toArray();

    // Событие 1: 04:00 - 06:00 → обрезается до 05:30 - 06:00 = 30 мин
    // Событие 2: 15:00 - 17:00 → обрезается до 15:00 - 15:30 = 30 мин
    // Итого: 60 мин => 10%
    expect((float)$serialized['workload'])->toBeGreaterThan(9.99)->toBeLessThan(10.01);
});

test('workload returns 100 when room fully occupied', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // Событие на весь расширенный рабочий день 05:30 - 15:30
    $event1 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event1, [
        'start_date' => $today->copy()->setTime(5, 30),
        'end_date' => $today->copy()->setTime(15, 30),
    ]);

    $room->load('office');
    $serialized = $room->toArray();

    expect((float)$serialized['workload'])->toBeGreaterThan(99.9)->toBeLessThan(100.1);
});

test('complex schedule with mixed gaps produces correct workload', function () {
    $user = User::factory()->create();

    $office = Office::create([
        'name' => 'Test Office',
        'timezone' => 'Asia/Yekaterinburg',
        'start_time' => '06:00:00',
        'end_time' => '15:00:00',
    ]);

    $room = Room::create([
        'name' => 'Test Room',
        'capacity' => 10,
        'office_id' => $office->id,
    ]);

    $today = now()->startOfDay();

    // Утро: 3 события подряд с мелкими интервалами (должны слиться в один блок)
    $event1 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event1, [
        'start_date' => $today->copy()->setTime(6, 0),
        'end_date' => $today->copy()->setTime(7, 0),
    ]);

    $event2 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event2, [
        'start_date' => $today->copy()->setTime(7, 5),
        'end_date' => $today->copy()->setTime(8, 0),
    ]);

    $event3 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event3, [
        'start_date' => $today->copy()->setTime(8, 5),
        'end_date' => $today->copy()->setTime(9, 0),
    ]);

    // День: 1 событие отдельно (разрыв > 10 мин)
    $event4 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event4, [
        'start_date' => $today->copy()->setTime(11, 0),
        'end_date' => $today->copy()->setTime(12, 0),
    ]);

    // Вечер: 2 события с мелким интервалом (должны слиться)
    $event5 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event5, [
        'start_date' => $today->copy()->setTime(14, 0),
        'end_date' => $today->copy()->setTime(14, 30),
    ]);

    $event6 = Event::factory()->create(['user_id' => $user->id]);
    $room->events()->attach($event6, [
        'start_date' => $today->copy()->setTime(14, 35),
        'end_date' => $today->copy()->setTime(15, 0),
    ]);

    $room->load('office');
    $serialized = $room->toArray();

    // Блок 1 (утро): 06:00 - 09:00 = 180 мин
    // Блок 2 (день): 11:00 - 12:00 = 60 мин
    // Блок 3 (вечер): 14:00 - 15:00 = 60 мин
    // Итого: 300 мин => 50%
    expect((float)$serialized['workload'])->toBeGreaterThan(49.9)->toBeLessThan(50.1);
});
