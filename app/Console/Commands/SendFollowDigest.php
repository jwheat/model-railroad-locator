<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Layout;
use App\Models\Post;
use App\Models\User;
use App\Notifications\FollowDigestNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SendFollowDigest extends Command
{
    protected $signature = 'notifications:follow-digest';

    protected $description = 'Queue a weekly digest of new stories from followed layouts, clubs, and members.';

    public function handle(): int
    {
        $queued = 0;

        User::query()
            ->where('notify_follow_digest', true)
            ->whereHas('follows')
            ->chunkById(100, function (Collection $users) use (&$queued) {
                foreach ($users as $user) {
                    $since = $user->digest_sent_at ?? now()->subWeek();
                    $posts = $this->newPostsFor($user, $since);

                    if ($posts->isNotEmpty()) {
                        $user->notify(new FollowDigestNotification($posts));
                        $queued++;
                    }

                    $user->forceFill(['digest_sent_at' => now()])->save();
                }
            });

        $this->info("Queued {$queued} follow digest(s).");

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, Post>
     */
    private function newPostsFor(User $user, Carbon $since): Collection
    {
        $layoutIds = $user->follows()
            ->where('followable_type', Layout::class)
            ->pluck('followable_id');

        $clubIds = $user->follows()
            ->where('followable_type', Club::class)
            ->pluck('followable_id');

        $memberIds = $user->follows()
            ->where('followable_type', User::class)
            ->pluck('followable_id');

        return Post::query()
            ->published()
            ->where('published_at', '>', $since)
            ->where('user_id', '!=', $user->id)
            ->where(function ($query) use ($layoutIds, $clubIds, $memberIds) {
                $query->whereIn('layout_id', $layoutIds)
                    ->orWhereIn('club_id', $clubIds)
                    ->orWhereIn('user_id', $memberIds);
            })
            ->with(['author', 'layout'])
            ->latest('published_at')
            ->take(25)
            ->get();
    }
}
