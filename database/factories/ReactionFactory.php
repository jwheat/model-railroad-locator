<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use App\ReactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reaction>
 */
class ReactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reactionable_type' => Post::class,
            'reactionable_id' => Post::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(ReactionType::cases()),
        ];
    }
}
