<?php

namespace Tests\Feature;

use App\Models\Layout;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        Layout::factory()->featured()->create();
        Post::factory()->create();

        $this->get('/')
            ->assertOk()
            ->assertSee('Featured layouts')
            ->assertSee('Latest from the workbench');
    }

    public function test_layout_directory_loads(): void
    {
        $this->get('/layouts')
            ->assertOk()
            ->assertSee('Find a railroad near you');
    }

    public function test_layout_page_loads_for_public_layout(): void
    {
        $layout = Layout::factory()->create();

        $this->get(route('layouts.show', $layout))
            ->assertOk()
            ->assertSee($layout->name);
    }

    public function test_post_page_loads_for_published_post(): void
    {
        $post = Post::factory()->create(['title' => 'A finished yard']);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('A finished yard');
    }

    public function test_draft_post_is_not_visible_to_guests(): void
    {
        $post = Post::factory()->draft()->create();

        $this->get(route('posts.show', $post))->assertForbidden();
    }

    public function test_member_profile_loads(): void
    {
        $user = User::factory()->create();

        $this->get(route('members.show', $user))
            ->assertOk()
            ->assertSee($user->displayName());
    }

    public function test_private_layout_is_not_visible_to_guests(): void
    {
        $layout = Layout::factory()->private()->create();

        $this->get(route('layouts.show', $layout))->assertForbidden();
    }

    public function test_admin_panel_requires_an_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin')
            ->assertOk();
    }
}
