<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Layout;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

abstract class CommentActivityNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Comment $comment) {}

    abstract protected function preference(): string;

    abstract protected function headline(object $notifiable): string;

    abstract protected function subject(object $notifiable): string;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->wantsNotification($this->preference())
            ? ['database', 'mail']
            : ['database'];
    }

    /**
     * @return array<string, string>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->headline($notifiable),
            'message' => Str::limit($this->comment->body, 140),
            'url' => $this->url(),
            'icon' => 'comment',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject($notifiable))
            ->greeting('Hello '.$notifiable->displayName().',')
            ->line($this->headline($notifiable).':')
            ->line('> '.Str::limit($this->comment->body, 300))
            ->action('View the conversation', $this->url())
            ->line('[Manage your notification preferences]('.route('notifications.preferences').')');
    }

    protected function url(): string
    {
        $commentable = $this->comment->commentable;

        return match (true) {
            $commentable instanceof Post => route('posts.show', $commentable).'#comments',
            $commentable instanceof Layout => route('layouts.show', $commentable).'#comments',
            default => route('home'),
        };
    }

    protected function subjectLabel(): string
    {
        $commentable = $this->comment->commentable;

        return match (true) {
            $commentable instanceof Post => '"'.$commentable->title.'"',
            $commentable instanceof Layout => $commentable->name,
            default => 'your content',
        };
    }
}
