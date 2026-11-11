<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'user_id' => User::factory(),
            'layout_id' => null,
            'club_id' => null,
            'title' => $title,
            'excerpt' => fake()->optional()->sentence(18),
            'body' => collect(fake()->paragraphs(4, true))->implode("\n\n"),
            'status' => PostStatus::Published,
            'published_at' => fake()->dateTimeBetween('-6 months'),
            'cover_image_path' => null,
            'allow_comments' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }
}
