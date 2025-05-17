<?php

namespace App\Models;

enum StatusRoom: string
{
    case FREE = 'FREE';
    case BUSY = 'BUSY';
    case SOON_BUSY = 'SOON_BUSY';

    public function getDescription(): string
    {
        return match ($this) {
            self::FREE => 'Свободна',
            self::BUSY => 'Занята',
            self::SOON_BUSY => 'Скоро будет занята',
        };
    }
}
