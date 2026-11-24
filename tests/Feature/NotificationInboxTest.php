<?php

namespace Tests\Feature;

use App\Livewire\NotificationPreferences;
use App\Models\Comment;
use App\Models\Layout;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_inbox_requires_authentication(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
    }

    public function test_visiting_the_inbox_marks_notifications_as_read(): void
    {
        $owner = User::factory()->create();
        $commenter = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);
        $comment = Comment::withoutEvents(fn () => Comment::factory()->create([
            'commentable_type' => Layout::class,
            'commentable_id' => $layout->id,
            'user_id' => $commenter->id,
        ]));

        $owner->notifyNow(new NewCommentNotification($comment));

        $this->assertSame(1, $owner->unreadNotifications()->count());

        $this->actingAs($owner)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee($layout->name);

        $this->assertSame(0, $owner->fresh()->unreadNotifications()->count());
    }

    public function test_member_can_update_email_preferences(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationPreferences::class)
            ->set('comments', false)
            ->set('digest', false)
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertFalse($user->notify_comments);
        $this->assertTrue($user->notify_replies);
        $this->assertFalse($user->notify_follow_digest);
    }

    public function test_signed_unsubscribe_link_disables_the_preference(): void
    {
        $user = User::factory()->create();

        $url = URL::signedRoute('notifications.unsubscribe', [
            'user' => $user,
            'type' => 'follow_digest',
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('unsubscribed');

        $this->assertFalse($user->fresh()->notify_follow_digest);
    }

    public function test_unsubscribe_requires_a_valid_signature(): void
    {
        $user = User::factory()->create();

        $this->get(route('notifications.unsubscribe', [
            'user' => $user,
            'type' => 'follow_digest',
        ]))->assertForbidden();
    }
}
