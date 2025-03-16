<?php

namespace App\Models;

use Database\Factories\RaspisanieFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $event_id
 * @property int $room_id
 * @property string|Carbon $start_date
 * @property string|Carbon $end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read TFactory|null $use_factory
 * @method static RaspisanieFactory factory($count = null, $state = [])
 * @method static Builder<static>|Raspisanie newModelQuery()
 * @method static Builder<static>|Raspisanie newQuery()
 * @method static Builder<static>|Raspisanie query()
 * @method static Builder<static>|Raspisanie whereCreatedAt($value)
 * @method static Builder<static>|Raspisanie whereEndDate($value)
 * @method static Builder<static>|Raspisanie whereEventId($value)
 * @method static Builder<static>|Raspisanie whereId($value)
 * @method static Builder<static>|Raspisanie whereRoomId($value)
 * @method static Builder<static>|Raspisanie whereStartDate($value)
 * @method static Builder<static>|Raspisanie whereUpdatedAt($value)
 * @method static Builder<static>|Raspisanie whereUserId($value)
 * @mixin Eloquent
 */
class Raspisanie extends Pivot
{
    /** @use HasFactory<RaspisanieFactory> */
    use HasFactory;

    protected $table = 'raspisanies';

    protected $fillable = [
        'start_date', // Поле для даты начала
        'end_date',   // Поле для даты окончания
        'room_id',    // Поле для ID комнаты
        'event_id',   // Поле для ID события
    ];

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

}
