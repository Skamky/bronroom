<?php

namespace Database\Seeders;

use DateTimeZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

    }
}
