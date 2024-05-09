<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'banner' => fake()->image(),
            'level' => fake()->randomElement(['A2', 'B1', 'B2', 'C1', 'C2']),
            'capacity' => fake()->numberBetween(10,20),
            'lessons_time' => fake()->time(),
            'lesson_days' => fake()->randomElement(['l-m', 'm-j', 'v']),
            'teacher_id'=> '01'
        ];
    }
}
