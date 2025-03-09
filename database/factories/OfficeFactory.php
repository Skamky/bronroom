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
        return [
            'name' => $this->faker->domainWord . ' ' . $timezone,
            'timezone' => $timezone,
        ];
    }
}
