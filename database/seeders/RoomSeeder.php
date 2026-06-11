<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = Office::all();

        if ($offices->isEmpty()) {
            $this->command->error('Сначала запустите OfficeSeeder для создания офисов.');

            return;
        }

        // Переговорные комнаты с понятными названиями и описаниями
        $meetingRooms = collect([
            [
                'name' => 'Переговорная 101',
                'description' => 'Комната для небольших встреч и собеседований. Тихое пространство с удобными креслами.',
                'capacity' => 2,
            ],
            [
                'name' => 'Переговорная 102',
                'description' => 'Переговорная комната для встреч с командой. Оборудована доской для заметок и маркерами.',
                'capacity' => 4,
            ],
            [
                'name' => 'Переговорная 103',
                'description' => 'Комната для встреч с клиентами и партнёрами. Проектор и система видео-связи.',
                'capacity' => 6,
            ],
            [
                'name' => 'Переговорная 104',
                'description' => 'Презентационная комната для демонстраций. Большой экран и профессиональное оборудование.',
                'capacity' => 8,
            ],
            [
                'name' => 'Переговорная 105',
                'description' => 'Комната для мозговых штурмов и креативных сессий. Стены для стикеров.',
                'capacity' => 6,
            ],
            [
                'name' => 'Переговорная 106',
                'description' => 'Комната для интервью и закрытых собеседований. Звукоизоляция и конфиденциальность.',
                'capacity' => 2,
            ],
            [
                'name' => 'Конференц-зал А',
                'description' => 'Большой зал для общих собраний и тренингов. Мультимедийное оборудование и микрофоны.',
                'capacity' => 12,
            ],
            [
                'name' => 'Конференц-зал Б',
                'description' => 'Конференц-зал с круговой рассадкой. Идеален для рабочих сессий и планирования.',
                'capacity' => 10,
            ],
        ]);

        // Распределяем переговорные по офисам
        $offices->each(function ($office) use ($meetingRooms) {
            $meetingRooms->each(function ($roomData) use ($office) {
                Room::updateOrCreate(
                    [
                        'name' => $roomData['name'],
                        'office_id' => $office->id,
                    ],
                    [
                        'description' => $roomData['description'],
                        'capacity' => $roomData['capacity'],
                    ]
                );
            });
        });
    }
}
