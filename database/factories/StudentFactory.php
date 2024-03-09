<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
        return [
            'full_name' => fake()->name(),
            'surname' => fake()->name(),
            'profile_photo' => fake()->image(),
            'level' => fake()->randomElement(['A2', 'B1', 'B2', 'C1', 'C2']),
            'birth_date' => fake()->date(),
            'user_id' => User::factory()
        ];
    }
}
