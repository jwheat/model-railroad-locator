<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'marta@example.com']);

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect(route('login'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_with_a_valid_token_allows_signing_in_with_the_new_password(): void
    {
        $user = User::factory()->create(['email' => 'marta@example.com']);

        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])->assertRedirect(route('login'));

        $this->assertAuthenticated();

        $this->post('/logout');
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-password123',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_reset_password_fails_with_an_invalid_token(): void
    {
        User::factory()->create(['email' => 'marta@example.com']);

        $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => 'marta@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
