<?php

namespace Database\Seeders;

use App\Models\Office;
use DateTimeZone;
use Exception;
use Illuminate\Database\Seeder;
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
            ],
            [
                'name' => 'Офис Москвы',
                'timezone' => 'Europe/Moscow'
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
