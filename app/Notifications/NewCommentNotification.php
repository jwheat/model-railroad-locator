<?php

namespace App\Notifications;

class NewCommentNotification extends CommentActivityNotification
{
    protected function preference(): string
    {
        return 'comments';
    }

    protected function headline(object $notifiable): string
    {
        return ($this->comment->user?->displayName() ?? 'Someone').' commented on '.$this->subjectLabel();
    }

    protected function subject(object $notifiable): string
    {
        return 'New comment on '.$this->subjectLabel();
    }
}
