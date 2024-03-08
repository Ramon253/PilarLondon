<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\comment_post>
 */
class Post_commentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->text(),
            'public' => fake()->boolean,
            'user_id' => User::factory(),
            'Parent_id' => \App\Models\Post_comment::factory()
        ];
    }
}
