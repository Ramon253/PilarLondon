<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SolutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(),
            'note' => fake()->randomNumber([1 - 10]),
            'student_id' => Student::factory(),
            'assignment_id' => Assignment::factory(),
        ];
    }
}
