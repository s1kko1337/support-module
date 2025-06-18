<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $education = ['общее среднее','среднее специальное','высшее'];
        $education_form = ['бюджет','целевое','платное'];
        return [
            'group_id' => 1,
            'name' => $this->faker->name(),
            'surname' => $this->faker->lastName(),
            'patronymic' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->date(),
            'phone_number' => $this->faker->phoneNumber(),
            'citizenship' => $this->faker->country(),
            'education' => $this->faker->randomElement($education),
            'education_form' => $this->faker->randomElement($education_form),
        ];
    }
}
