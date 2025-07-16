<?php

namespace App\Models;

enum StatusRoom: string
{
    case FREE = 'FREE';
    case BUSY = 'BUSY';
    case BOOKED = 'BOOKED';

    public function getDescription(): string
    {
        return match ($this) {
            self::FREE => 'Свободна',
            self::BUSY => 'Занята',
            self::BOOKED => 'Забронирована вами',
        };
    }
}
