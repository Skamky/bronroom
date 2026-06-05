<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Room;
use App\Models\User;
use App\Services\EventService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function __construct(
        private readonly EventService $eventService
    )
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDate = Carbon::now();
        $endDate = $currentDate->addMonth()->endOfMonth();

        $rooms = Room::with('office')->get();
        $users = User::all();

        foreach ($users as $user) {
            foreach ($rooms as $room) {
                $office = $room->office;
                if (!$office) {
                    continue;
                }

                $this->generateEventsForRoom($user, $room, $office, $currentDate, $endDate);
            }
        }
    }

    /**
     * Generate events for a single room with overlap checking and office time constraints.
     */
    private function generateEventsForRoom(
        User   $user,
        Room   $room,
               $office,
        Carbon $currentDate,
        Carbon $endDate
    ): void
    {
        $maxAttempts = 50;

        for ($i = 0; $i < 10; $i++) {
            $created = false;
            $attempt = 0;

            while (!$created && $attempt < $maxAttempts) {
                $attempt++;

                $startTime = fake()->dateTimeBetween('-1 month', $endDate);
                $startTime = Carbon::instance($startTime)->minute(intdiv($startTime->format('i'), 5) * 5)->second(0);

                $duration = fake()->numberBetween(30, 180);
                $duration = intdiv($duration, 30) * 30;

                $endTime = $startTime->copy()->addMinutes($duration);

                // Ограничение рамками рабочего дня офиса для каждой даты
                $officeStart = $startTime->copy()->setTimeFrom($office->start_time);
                $officeEnd = $startTime->copy()->setTimeFrom($office->end_time);

                // Начало не раньше office.start_time
                if ($startTime->lessThan($officeStart)) {
                    $startTime = $officeStart;
                }

                // Если startTime сдвинулся за endTime — пропускаем
                if ($startTime->greaterThanOrEqualTo($endTime)) {
                    continue;
                }

                // Конец не позже office.end_time
                if ($endTime->greaterThan($officeEnd)) {
                    $endTime = $officeEnd;
                    $duration = $startTime->diffInMinutes($endTime);

                    // Если время слишком короткое для события — пропускаем
                    if ($duration < 30) {
                        continue;
                    }
                }

                // Проверка пересечений через EventService
                if ($this->eventService->checkEventOverlap($room, $startTime->toDateTimeString(), $endTime->toDateTimeString())) {
                    // Пересечение есть — перегенерируем
                    continue;
                }

                // Все проверки пройдены — создаём событие
                Event::factory()
                    ->hasAttached(
                        $room,
                        [
                            'start_date' => $startTime,
                            'end_date' => $endTime,
                        ]
                    )
                    ->for($user)
                    ->create();

                $created = true;
            }
        }
    }
}
