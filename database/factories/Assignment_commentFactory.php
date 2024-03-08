<?php

namespace Database\Factories;

use App\Models\Assignment_comment;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment_comment>
 */
class Assignment_commentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content'=> fake()->text(),
            'public'=> fake()->boolean(),
            'user_id'=> User::factory(),
            'parent_id'=> Assignment_comment::factory(),
            'assignment_id'=> Assignment::factory(),

        ];
    }
}
