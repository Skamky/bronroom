<?php

namespace Tests\Helpers;

use App\Models\Event;
use App\Models\Room;
use App\Models\User;

class EventHelper
{
    public static function create(User $user, Room $room, $startTime, $endTime): Event
    {
        return Event::factory()
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
