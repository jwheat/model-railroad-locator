<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Layout;
use App\Models\Post;
use App\Models\User;
use App\Notifications\FollowDigestNotification;
use App\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FollowDigestTest extends TestCase
{
    use RefreshDatabase;

    public function test_followers_receive_a_digest_of_new_stories(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $follower = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);
        $layout->followers()->create(['user_id' => $follower->id]);

        Post::factory()->create([
            'user_id' => $owner->id,
            'layout_id' => $layout->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $this->artisan('notifications:follow-digest')->assertSuccessful();

        Notification::assertSentTo($follower, FollowDigestNotification::class);
        $this->assertNotNull($follower->fresh()->digest_sent_at);
    }

    public function test_following_a_club_or_member_also_triggers_a_digest(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $follower = User::factory()->create();
        $club = Club::factory()->create();

        $author->followers()->create(['user_id' => $follower->id]);
        $club->followers()->create(['user_id' => $follower->id]);

        Post::factory()->create([
            'user_id' => $author->id,
            'published_at' => now()->subHour(),
        ]);

        Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'club_id' => $club->id,
            'published_at' => now()->subHour(),
        ]);

        $this->artisan('notifications:follow-digest')->assertSuccessful();

        Notification::assertSentTo(
            $follower,
            FollowDigestNotification::class,
            fn (FollowDigestNotification $notification) => $notification->posts->count() === 2,
        );
    }

    public function test_digest_is_skipped_when_the_member_opted_out(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $follower = User::factory()->create(['notify_follow_digest' => false]);
        $layout = Layout::factory()->create(['user_id' => $owner->id]);
        $layout->followers()->create(['user_id' => $follower->id]);

        Post::factory()->create([
            'user_id' => $owner->id,
            'layout_id' => $layout->id,
            'published_at' => now()->subDay(),
        ]);

        $this->artisan('notifications:follow-digest')->assertSuccessful();

        Notification::assertNotSentTo($follower, FollowDigestNotification::class);
    }

    public function test_members_do_not_get_their_own_stories_in_the_digest(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);
        $layout->followers()->create(['user_id' => $owner->id]);

        Post::factory()->create([
            'user_id' => $owner->id,
            'layout_id' => $layout->id,
            'published_at' => now()->subDay(),
        ]);

        $this->artisan('notifications:follow-digest')->assertSuccessful();

        Notification::assertNotSentTo($owner, FollowDigestNotification::class);
    }

    public function test_the_digest_only_includes_stories_since_the_last_run(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $follower = User::factory()->create(['digest_sent_at' => now()->subDay()]);
        $layout = Layout::factory()->create(['user_id' => $owner->id]);
        $layout->followers()->create(['user_id' => $follower->id]);

        Post::factory()->create([
            'user_id' => $owner->id,
            'layout_id' => $layout->id,
            'published_at' => now()->subWeek(),
        ]);

        $this->artisan('notifications:follow-digest')->assertSuccessful();

        Notification::assertNotSentTo($follower, FollowDigestNotification::class);
    }

    public function test_a_second_run_does_not_repeat_the_same_stories(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $follower = User::factory()->create();
        $layout = Layout::factory()->create(['user_id' => $owner->id]);
        $layout->followers()->create(['user_id' => $follower->id]);

        Post::factory()->create([
            'user_id' => $owner->id,
            'layout_id' => $layout->id,
            'published_at' => now()->subDay(),
        ]);

        $this->artisan('notifications:follow-digest')->assertSuccessful();
        $this->artisan('notifications:follow-digest')->assertSuccessful();

        Notification::assertSentToTimes($follower, FollowDigestNotification::class, 1);
    }
}
