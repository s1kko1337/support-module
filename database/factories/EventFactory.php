<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
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
        $category = ['Учебное', 'Внеучебное'];
        return [
            'group_id' => 1,
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement($category),
            'result' => $this->faker->text(),
            'date' => $this->faker->dateTime(),
        ];
    }
}
