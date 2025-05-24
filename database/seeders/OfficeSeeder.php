<?php

namespace Database\Seeders;

use App\Models\Office;
use DateTimeZone;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $offices = new Collection([
            [
                'name' => 'Офис Уфы',
                'timezone' => 'Asia/Yekaterinburg',
                'start_time' => Carbon::createFromTime(10, 0, 0, 'Asia/Yekaterinburg')->utc(),
                'end_time' => Carbon::createFromTime(19, 0, 0, 'Asia/Yekaterinburg')->utc(),
            ],
            [
                'name' => 'Офис Москвы',
                'timezone' => 'Europe/Moscow',
                'start_time' => Carbon::createFromTime(9, 0, 0, 'Europe/Moscow')->utc(),
                'end_time' => Carbon::createFromTime(18, 0, 0, 'Europe/Moscow')->utc(),
            ]
        ]);

        $timezones = collect(DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, 'RU'));


        $offices->first(function ($office) use ($timezones) {
            if (!$timezones->contains($office['timezone'])) {
                throw new Exception("Таймзона {$office['timezone']} не содержится в $timezones");
            }
            return false; // Продолжаем итерацию
        });

        Office::query()->upsert($offices->all(), ['name', 'timezone']);

    }
}
