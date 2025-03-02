<?php

namespace Database\Factories;

use App\Models\Office;
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
            'name' => $office->name . ' K' . $this->faker->unique()->numberBetween(100, 199),
            'description' => $this->faker->sentence,
            'capacity' => $this->faker->numberBetween(1, 10),
            'office_id' => $office->id ,
        ];
    }

    public function forOffice(Office $office): Factory|RoomFactory
    {
        return $this->state(function (array $attributes) use ($office) {
            return [
                'office_id' => $office->id,
                'name' => $office->name . ' K' . $this->faker->unique()->numberBetween(100, 199),
            ];
        });
    }
}



