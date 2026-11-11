<?php

namespace Database\Factories;

use App\LayoutScale;
use App\LayoutVisibility;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Layout>
 */
class LayoutFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'club_id' => null,
            'name' => fake()->words(3, true).' & Western',
            'description' => fake()->paragraphs(2, true),
            'scale' => fake()->randomElement(LayoutScale::cases()),
            'era' => fake()->randomElement(['Steam', 'Transition', 'Modern', '1950s']),
            'city' => fake()->city(),
            'region' => fake()->stateAbbr(),
            'country' => 'US',
            'latitude' => fake()->latitude(25.0, 48.5),
            'longitude' => fake()->longitude(-124.0, -67.0),
            'visibility' => LayoutVisibility::Public,
            'is_featured' => false,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => LayoutVisibility::Private,
        ]);
    }
}
