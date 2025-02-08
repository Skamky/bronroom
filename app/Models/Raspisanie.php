<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int $room_id
 * @property string $start_date
 * @property string $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Database\Factories\RaspisanieFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raspisanie whereUserId($value)
 * @mixin \Eloquent
 */
class Raspisanie extends Model
{
    /** @use HasFactory<\Database\Factories\RaspisanieFactory> */
    use HasFactory;
}
