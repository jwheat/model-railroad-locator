<?php

namespace App\Notifications;

class ReplyNotification extends CommentActivityNotification
{
    protected function preference(): string
    {
        return 'replies';
    }

    protected function headline(object $notifiable): string
    {
        return ($this->comment->user?->displayName() ?? 'Someone').' replied to your comment';
    }

    protected function subject(object $notifiable): string
    {
        return 'New reply to your comment';
    }
}
