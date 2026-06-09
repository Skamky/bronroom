<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $capacity
 * @property int $office_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TFactory|null $use_factory
 *
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
 *
 * @mixin \Eloquent
 */
class Room extends Model
{
    use AsSource;

    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',       // Поле для названия события
        'description', // Поле для описания события
        'user_id',    // Поле для ID пользователя
        'capacity',
        'office_id',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'raspisanies')
            ->withPivot('start_date', 'end_date')
            ->using(Raspisanie::class);
    }

    public function raspisanie()
    {
        return $this->belongsTo(Raspisanie::class);
    }

    protected function getStatus(?Carbon $date = null): StatusRoom
    {
        if (is_null($date)) {
            $date = now();
        }

        $events = $this->events()->where(function ($query) use ($date) {
            $query->where('raspisanies.start_date', '<=', $date)
                ->where('raspisanies.end_date', '>=', $date);
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

    public function toArray()
    {
        $array = parent::toArray();
        $array['status'] = $this->getStatus();
        $array['workload'] = round($this->getWorkload(), 2);

        return $array;
    }

    /**
     * Объединяет близкие интервалы событий и считает загруженность.
     * События, расстояние между которыми <= mergeThreshold, объединяются в один блок.
     */
    /**
     * Объединяет близкие интервалы событий и считает загруженность.
     */
    private function getWorkload(): float
    {
        $today = now();
        $office = $this->office;

        // Порог в минуты: если между событиями <= этого значения, считаем их связанными
        // Можно вынести в конфиг: config('app.merge_threshold', 10)
        $mergeThreshold = 10;

        $workStart = $office->start_time;
        $workEnd = $office->end_time;

        // Расширяем границы рабочего дня для учета событий, которые начинаются чуть раньше или заканчиваются позже
        $expandedStart = $today->copy()->setTimeFrom($workStart)->subMinutes(30);
        $expandedEnd = $today->copy()->setTimeFrom($workEnd)->addMinutes(30);

        // Получаем события с pivot данными
        $events = $this->events()
            ->wherePivot('start_date', '<', $expandedEnd)
            ->wherePivot('end_date', '>', $expandedStart)
            ->get();

        if ($events->isEmpty()) {
            return 0;
        }

        // 1. Формируем массив интервалов [start_timestamp, end_timestamp]
        // Используем timestamp, чтобы избежать проблем со сравнением объектов Carbon
        $intervals = [];
        foreach ($events as $event) {
            $pivotStart = $event->pivot->start_date;
            $pivotEnd = $event->pivot->end_date;

            // Обрезаем интервалы по границам расширенного рабочего дня
            // Если событие началось раньше expandedStart, считаем от expandedStart
            $startTs = max($pivotStart->timestamp, $expandedStart->timestamp);
            // Если событие закончилось позже expandedEnd, считаем до expandedEnd
            $endTs = min($pivotEnd->timestamp, $expandedEnd->timestamp);

            // Добавляем только валидные интервалы (где конец больше начала)
            if ($endTs > $startTs) {
                $intervals[] = [$startTs, $endTs];
            }
        }

        if (empty($intervals)) {
            return 0;
        }

        // 2. Сортируем интервалы по времени начала
        usort($intervals, function ($a, $b) {
            return $a[0] <=> $b[0];
        });

        // 3. Объединяем пересекающиеся и близкие интервалы
        $merged = [];
        $currentStart = $intervals[0][0];
        $currentEnd = $intervals[0][1];

        for ($i = 1; $i < count($intervals); $i++) {
            $nextStart = $intervals[$i][0];
            $nextEnd = $intervals[$i][1];

            // Переводим порог в секунды для сравнения с timestamp
            $thresholdSeconds = $mergeThreshold * 60;

            // Если следующее событие начинается раньше, чем закончится текущее + порог
            // то есть: nextStart <= currentEnd + threshold
            if ($nextStart <= $currentEnd + $thresholdSeconds) {
                // Объединяем: продлеваем текущий конец, если следующий заканчивается позже
                $currentEnd = max($currentEnd, $nextEnd);
            } else {
                // Разрыв больше порога, сохраняем текущий блок и начинаем новый
                $merged[] = [$currentStart, $currentEnd];
                $currentStart = $nextStart;
                $currentEnd = $nextEnd;
            }
        }
        // Не забываем добавить последний блок
        $merged[] = [$currentStart, $currentEnd];

        // 4. Считаем общую занятую минуту по объединённым интервалам
        $occupiedSeconds = 0;
        foreach ($merged as $interval) {
            $occupiedSeconds += ($interval[1] - $interval[0]);
        }

        // Переводим в минуты
        $occupiedMinutes = $occupiedSeconds / 60;

        // Общее время расширенного рабочего дня в минутах
        $totalMinutes = $expandedStart->diffInMinutes($expandedEnd);

        return $totalMinutes > 0 ? ($occupiedMinutes / $totalMinutes) * 100 : 0;
    }
}
