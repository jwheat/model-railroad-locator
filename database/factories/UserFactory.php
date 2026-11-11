<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'display_name' => null,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'bio' => fake()->optional()->paragraph(),
            'city' => fake()->city(),
            'region' => fake()->stateAbbr(),
            'country' => 'US',
            'is_admin' => false,
        ];
    }

    public function admin(): static
    {
        return $this->afterMaking(fn (User $user) => $user->forceFill(['is_admin' => true]))
            ->afterCreating(fn (User $user) => $user->forceFill(['is_admin' => true])->saveQuietly());
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
