<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mediable_type' => Post::class,
            'mediable_id' => Post::factory(),
            'type' => 'image',
            'disk' => 'public',
            'path' => 'media/'.fake()->uuid().'.jpg',
            'alt' => fake()->sentence(5),
            'caption' => fake()->optional()->sentence(),
            'width' => 1600,
            'height' => 900,
            'sort_order' => 0,
        ];
    }

    public function embed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'video_embed',
            'disk' => null,
            'path' => null,
            'url' => 'https://www.youtube.com/watch?v='.fake()->regexify('[A-Za-z0-9_-]{11}'),
            'width' => null,
            'height' => null,
        ]);
    }
}
