<?php

namespace Tests\Feature;

use App\Livewire\ProfileEditor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_to_edit_their_profile(): void
    {
        $this->get(route('account.profile'))->assertRedirect(route('login'));
    }

    public function test_authenticated_member_can_update_their_profile(): void
    {
        $user = User::factory()->create([
            'display_name' => 'Marta Reyes',
            'bio' => 'Old bio.',
            'city' => 'Norfolk',
            'region' => 'VA',
            'country' => 'US',
        ]);

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->set('displayName', 'Marta R.')
            ->set('bio', 'N scale modular modeler, and counting.')
            ->set('city', 'Hampton')
            ->set('region', 'VA')
            ->set('country', 'US')
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Marta R.', $user->display_name);
        $this->assertSame('N scale modular modeler, and counting.', $user->bio);
        $this->assertSame('Hampton', $user->city);
    }

    public function test_profile_bio_is_limited_to_5000_characters(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->set('bio', str_repeat('a', 5001))
            ->call('save')
            ->assertHasErrors(['bio' => 'max']);
    }
}
