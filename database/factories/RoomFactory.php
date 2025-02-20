<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $office = Office::query()->inRandomOrder()->first();
        return [
            'name' => $office->name.' '.$this->faker->word,
            'description' => $this->faker->sentence,
            'capacity' => $this->faker->numberBetween(1, 10),
            'office_id' => $office->id ,
        ];
    }


}
