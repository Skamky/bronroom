<?php

namespace Database\Factories;

use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
class OfficeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timezone = $this->faker->randomElement(DateTimeZone::listIdentifiers());
        $startHour = $this->faker->numberBetween(8, 10);
        $endHour = $this->faker->numberBetween(17, 19);
        return [
            'name' => $this->faker->domainWord . ' ' . $timezone,
            'timezone' => $timezone,
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $endHour),
        ];
    }
}
