<?php

namespace Tests\Feature;

use App\Livewire\LayoutEditor;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LayoutEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('layouts.create'))->assertRedirect(route('login'));
    }

    public function test_member_can_add_a_layout(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(LayoutEditor::class)
            ->set('name', 'Test Junction')
            ->set('description', 'A small switching layout.')
            ->set('scale', 'HO')
            ->set('era', '1950s')
            ->set('city', 'Portland')
            ->set('region', 'OR')
            ->set('latitude', '45.5152')
            ->set('longitude', '-122.6784')
            ->set('visibility', 'public')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $layout = Layout::query()->firstWhere('name', 'Test Junction');

        $this->assertNotNull($layout);
        $this->assertSame($user->id, $layout->user_id);
        $this->assertSame('test-junction', $layout->slug);
        $this->assertSame($user->id, $layout->owner->id);
        $this->assertEqualsWithDelta(45.5152, $layout->latitude, 0.0000001);
    }

    public function test_member_cannot_edit_another_members_layout(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($other)
            ->test(LayoutEditor::class, ['layout' => $layout])
            ->assertForbidden();
    }

    public function test_layout_name_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(LayoutEditor::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }
}
