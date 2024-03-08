<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->name(),
            'name' => fake()->name(),
            'description' =>fake()->text(),
            'group_id' => Group::factory(),
            'teacher_id' => Teacher::factory()
        ];
    }
}
