<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CRS-###??')),
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'capacity' => fake()->numberBetween(10, 60),
            'active' => true,
        ];
    }
}
