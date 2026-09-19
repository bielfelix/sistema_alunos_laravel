<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'birth_date' => fake()->dateTimeBetween('-45 years', '-16 years')->format('Y-m-d'),
            'status' => StudentStatus::Active,
            'metadata' => null,
        ];
    }
}
