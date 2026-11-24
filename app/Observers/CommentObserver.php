<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\Layout;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use App\Notifications\ReplyNotification;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $comment->loadMissing(['user', 'parent.user', 'commentable']);

        if (! $comment->user) {
            return;
        }

        $notified = [];

        if ($comment->parent_id !== null && $comment->parent?->user) {
            $recipient = $comment->parent->user;

            if ($recipient->id !== $comment->user_id) {
                $recipient->notify(new ReplyNotification($comment));
                $notified[$recipient->id] = true;
            }
        }

        $owner = $this->ownerOf($comment);

        if ($owner && $owner->id !== $comment->user_id && ! isset($notified[$owner->id])) {
            $owner->notify(new NewCommentNotification($comment));
        }
    }

    private function ownerOf(Comment $comment): ?User
    {
        return match (true) {
            $comment->commentable instanceof Post => $comment->commentable->author,
            $comment->commentable instanceof Layout => $comment->commentable->owner,
            default => null,
        };
    }
}
