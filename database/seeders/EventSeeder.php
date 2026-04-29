<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDate = Carbon::now();
        $endDate = $currentDate->addMonth()->endOfMonth();

        $rooms = Room::all();
        $users = User::all();
        for ($i = 0; $i < 10; $i++) {
            foreach ($users as $user) {

                foreach ($rooms as $room) {

                    $startTime = fake()->dateTimeBetween('-1 month', $endDate);
                    $startTime = Carbon::instance($startTime)->minute(intdiv($startTime->format('i'), 5) * 5)->second(0);

                    $duration = fake()->numberBetween(30, 180);
                    $duration = intdiv($duration, 30) * 30;

                    $endTime = Carbon::instance($startTime)->addMinutes($duration);
                    Event::factory()
                        ->hasAttached(
                            $room,
                            [
                                'start_date' => $startTime,
                                'end_date' => $endTime,
                            ]
                        )->for($user)
                        ->create();
                }
            }
        }
    }
}
