<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\AsSource;

/**
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $capacity
 * @property int $office_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Database\Factories\RoomFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;
    use AsSource;
    use SoftDeletes;

    protected $fillable = [
        'name',       // Поле для названия события
        'description', // Поле для описания события
        'user_id',    // Поле для ID пользователя
        'capacity',
        'office_id'
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'raspisanies')
            ->withPivot('start_date', 'end_date')
            ->using(Raspisanie::class);
    }

    public function raspisanie()
    {
        return $this->belongsTo(Raspisanie::class);
    }

    protected function getStatus(Carbon $date = null): StatusRoom
    {
        if (is_null($date)) {
            $date = now();
        }

        $events = $this->events()->where(function ($query) use ($date) {
            $query->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date);
        })->get();

        // Проверка на наличие событий в ближайшие 15 минут или занятость в данный момент
        if ($events->count() > 0) {
            /** @var Event $event */
            $event = $events->first();
            if ($event->user_id == Auth::id()) {
                return StatusRoom::BOOKED;
            } else {
                return StatusRoom::BUSY;
            }
        } else {
            return StatusRoom::FREE;
        }
    }

    public function getWorkload()
    {
        $today = now();
        $office = $this->office;

        $workStart = $office->start_time;
        $workEnd = $office->end_time;

        $expandedStart = $today->copy()->setTimeFrom($workStart)->subMinutes(30);
        $expandedEnd = $today->copy()->setTimeFrom($workEnd)->addMinutes(30);

        $events = $this->events()
            ->where(function ($query) use ($expandedStart, $expandedEnd) {
                $query->where('start_date', '<', $expandedEnd)
                    ->where('end_date', '>', $expandedStart);
            })
            ->get();

        $totalMinutes = $expandedStart->diffInMinutes($expandedEnd);
        $occupiedMinutes = $events->sum(function ($event) use ($expandedStart, $expandedEnd) {


            $start = max($event->pivot->start_date, $expandedStart);
            $end = min($event->pivot->end_date, $expandedEnd);
            return $start->diffInMinutes($end);
        });

        return $totalMinutes > 0 ? ($occupiedMinutes / $totalMinutes) * 100 : 0;

    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['status'] = $this->getStatus();
        $array['workload'] = $this->getWorkload();
        return $array;
    }
}
