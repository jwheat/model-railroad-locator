<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Club>
 */
class ClubFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->city().' Model Railroad Club',
            'description' => fake()->paragraph(),
            'city' => fake()->city(),
            'region' => fake()->stateAbbr(),
            'country' => 'US',
            'website' => fake()->optional()->url(),
            'is_verified' => fake()->boolean(20),
        ];
    }
}
