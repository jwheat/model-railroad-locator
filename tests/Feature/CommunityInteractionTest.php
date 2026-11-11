<?php

namespace Tests\Feature;

use App\Livewire\Comments;
use App\Livewire\ReactionBar;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CommunityInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_member_can_leave_a_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Livewire::actingAs($user)
            ->test(Comments::class, [
                'commentableType' => 'post',
                'commentableId' => $post->id,
            ])
            ->set('body', 'Great progress on the yard throat.')
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'user_id' => $user->id,
            'body' => 'Great progress on the yard throat.',
        ]);
    }

    public function test_comment_body_is_required(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Livewire::actingAs($user)
            ->test(Comments::class, [
                'commentableType' => 'post',
                'commentableId' => $post->id,
            ])
            ->call('addComment')
            ->assertHasErrors(['body' => 'required']);
    }

    public function test_member_can_delete_their_own_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Comments::class, [
                'commentableType' => 'post',
                'commentableId' => $post->id,
            ])
            ->call('deleteComment', $comment->id);

        $this->assertSoftDeleted($comment);
    }

    public function test_reaction_toggles_on_and_off(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Livewire::actingAs($user)
            ->test(ReactionBar::class, [
                'reactionableType' => 'post',
                'reactionableId' => $post->id,
            ])
            ->call('toggle', 'like');

        $this->assertDatabaseHas('reactions', [
            'reactionable_type' => Post::class,
            'reactionable_id' => $post->id,
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        Livewire::actingAs($user)
            ->test(ReactionBar::class, [
                'reactionableType' => 'post',
                'reactionableId' => $post->id,
            ])
            ->call('toggle', 'like');

        $this->assertDatabaseMissing('reactions', [
            'reactionable_type' => Post::class,
            'reactionable_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_a_member_can_only_hold_one_reaction_per_item(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Livewire::actingAs($user)
            ->test(ReactionBar::class, [
                'reactionableType' => 'post',
                'reactionableId' => $post->id,
            ])
            ->call('toggle', 'like')
            ->call('toggle', 'love');

        $this->assertDatabaseCount('reactions', 1);
        $this->assertDatabaseHas('reactions', [
            'user_id' => $user->id,
            'type' => 'love',
        ]);
    }
}
