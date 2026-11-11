<?php

namespace Tests\Feature;

use App\Livewire\PostEditor;
use App\Models\Post;
use App\Models\User;
use App\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_and_publish_a_story(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PostEditor::class)
            ->set('title', 'My first build report')
            ->set('body', '## Benchwork\n\nWe started framing today.')
            ->call('publish')
            ->assertHasNoErrors()
            ->assertRedirect();

        $post = Post::query()->firstWhere('title', 'My first build report');

        $this->assertNotNull($post);
        $this->assertSame($user->id, $post->user_id);
        $this->assertSame('my-first-build-report', $post->slug);
        $this->assertSame(PostStatus::Published, $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_body_is_required_to_publish_but_not_to_save_a_draft(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PostEditor::class)
            ->set('title', 'Work in progress')
            ->call('publish')
            ->assertHasErrors(['body' => 'required']);

        Livewire::actingAs($user)
            ->test(PostEditor::class)
            ->set('title', 'Work in progress')
            ->call('saveDraft')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'title' => 'Work in progress',
            'status' => 'draft',
        ]);
    }

    public function test_member_cannot_edit_another_members_story(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($other)
            ->test(PostEditor::class, ['post' => $post])
            ->assertForbidden();
    }
}
