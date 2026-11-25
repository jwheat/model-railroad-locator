<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Comment;
use App\Models\Layout;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login_page(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_the_admin_dashboard_loads(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin')
            ->assertOk();
    }

    public function test_members_cannot_access_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs(User::factory()->create())
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_admin_can_view_the_users_resource_pages(): void
    {
        $user = User::factory()->create(['name' => 'Casey Jordan']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Casey Jordan');

        $this->get('/admin/users/create')
            ->assertOk();
    }

    public function test_admin_can_view_the_posts_resource_pages(): void
    {
        $post = Post::factory()->create(['title' => 'A finished yard']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('A finished yard');

        $this->get('/admin/posts/create')
            ->assertOk();
    }

    public function test_admin_can_view_the_layouts_resource_pages(): void
    {
        $layout = Layout::factory()->create(['name' => 'Roundhouse railroad']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/layouts')
            ->assertOk()
            ->assertSee('Roundhouse railroad');

        $this->get('/admin/layouts/create')
            ->assertOk();
    }

    public function test_admin_can_view_the_clubs_resource_pages(): void
    {
        $club = Club::factory()->create(['name' => 'Norfolk N scale club']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/clubs')
            ->assertOk()
            ->assertSee('Norfolk N scale club');

        $this->get('/admin/clubs/create')
            ->assertOk();
    }

    public function test_admin_can_view_the_tags_resource_pages(): void
    {
        $tag = Tag::factory()->create(['name' => 'turnout']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/tags')
            ->assertOk()
            ->assertSee('turnout');

        $this->get('/admin/tags/create')
            ->assertOk();
    }

    public function test_admin_can_view_the_comments_resource_pages(): void
    {
        $comment = Comment::factory()->create(['body' => 'Great progress on the yard throat.']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/comments')
            ->assertOk()
            ->assertSee('Great progress on the yard throat.');

        $this->get('/admin/comments/create')
            ->assertOk();
    }

    public function test_admin_can_view_the_users_edit_page(): void
    {
        $user = User::factory()->create(['name' => 'Casey Jordan']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/users/'.$user->username.'/edit')
            ->assertOk();
    }

    public function test_admin_can_view_the_posts_edit_page(): void
    {
        $post = Post::factory()->create(['title' => 'A finished yard']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/posts/'.$post->slug.'/edit')
            ->assertOk();
    }

    public function test_admin_can_view_the_layouts_edit_page(): void
    {
        $layout = Layout::factory()->create(['name' => 'Roundhouse railroad']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/layouts/'.$layout->slug.'/edit')
            ->assertOk();
    }

    public function test_admin_can_view_the_clubs_edit_page(): void
    {
        $club = Club::factory()->create(['name' => 'Norfolk N scale club']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/clubs/'.$club->slug.'/edit')
            ->assertOk();
    }

    public function test_admin_can_view_the_tags_edit_page(): void
    {
        $tag = Tag::factory()->create(['name' => 'turnout']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/tags/'.$tag->id.'/edit')
            ->assertOk();
    }

    public function test_admin_can_view_the_comments_edit_page(): void
    {
        $comment = Comment::factory()->create(['body' => 'Great progress on the yard throat.']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/comments/'.$comment->id.'/edit')
            ->assertOk();
    }
}
