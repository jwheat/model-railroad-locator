<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Taylor Morgan',
            'username' => 'taylor',
            'email' => 'taylor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => 'taylor',
            'email' => 'taylor@example.com',
        ]);
    }

    public function test_registration_requires_a_unique_username(): void
    {
        User::factory()->create(['username' => 'taylor']);

        $this->post('/register', [
            'name' => 'Taylor Morgan',
            'username' => 'taylor',
            'email' => 'taylor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_a_member_can_sign_in_and_out(): void
    {
        $user = User::factory()->create([
            'email' => 'casey@example.com',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => 'casey@example.com',
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create(['email' => 'casey@example.com']);

        $this->post('/login', [
            'email' => 'casey@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
