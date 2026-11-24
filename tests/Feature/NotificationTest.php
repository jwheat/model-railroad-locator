<?php

namespace Tests\Feature;

use App\Livewire\Comments;
use App\Models\Comment;
use App\Models\Layout;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use App\Notifications\ReplyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_owner_is_notified_when_someone_comments(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $commenter = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($commenter)
            ->test(Comments::class, [
                'commentableType' => 'layout',
                'commentableId' => $layout->id,
            ])
            ->set('body', 'What a great track plan.')
            ->call('addComment');

        Notification::assertSentTo($owner, NewCommentNotification::class);
        Notification::assertNotSentTo($commenter, NewCommentNotification::class);
    }

    public function test_post_author_is_notified_when_someone_comments(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        Livewire::actingAs($commenter)
            ->test(Comments::class, [
                'commentableType' => 'post',
                'commentableId' => $post->id,
            ])
            ->set('body', 'Thanks for writing this up.')
            ->call('addComment');

        Notification::assertSentTo($author, NewCommentNotification::class);
    }

    public function test_comment_author_is_notified_when_someone_replies(): void
    {
        Notification::fake();

        $post = Post::factory()->create();
        $parentAuthor = User::factory()->create();
        $replier = User::factory()->create();

        $parent = Comment::factory()->create([
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'user_id' => $parentAuthor->id,
        ]);

        Livewire::actingAs($replier)
            ->test(Comments::class, [
                'commentableType' => 'post',
                'commentableId' => $post->id,
            ])
            ->set('body', 'Good question, here is what worked for me.')
            ->set('replyTo', $parent->id)
            ->call('addComment');

        Notification::assertSentTo($parentAuthor, ReplyNotification::class);
        Notification::assertNotSentTo($replier, ReplyNotification::class);
    }

    public function test_members_are_not_notified_about_their_own_comments(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($owner)
            ->test(Comments::class, [
                'commentableType' => 'layout',
                'commentableId' => $layout->id,
            ])
            ->set('body', 'Adding a note for myself.')
            ->call('addComment');

        Notification::assertNothingSent();
    }

    public function test_comment_email_can_be_disabled_while_database_notifications_remain(): void
    {
        Notification::fake();

        $owner = User::factory()->create(['notify_comments' => false]);
        $commenter = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($commenter)
            ->test(Comments::class, [
                'commentableType' => 'layout',
                'commentableId' => $layout->id,
            ])
            ->set('body', 'Nice work on the scenery.')
            ->call('addComment');

        Notification::assertSentTo(
            $owner,
            NewCommentNotification::class,
            fn ($notification, $channels) => $channels === ['database'],
        );
    }
}
