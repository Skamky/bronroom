<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::factory()->count(10)->create();
        //   Room::factory()->count(5)->create(['office_id'=>1]);
        //   Room::factory()->count(5)->create(['office_id'=>2]);

        //Room::factory()->count(5)->for(Office::whereId(2))->create();
    }
}
