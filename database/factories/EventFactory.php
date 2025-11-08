<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $title = $this->faker->words($this->faker->numberBetween(1, 3), true);
        return [
            'name' => "[{$this->faker->countryISOAlpha3()}] {$title}",
            'description' => $this->faker->text(),
        ];
    }


}
