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

        foreach ($offices as $office) {
            Room::factory(10)->forOffice($office)->create();
        }

    }
}
