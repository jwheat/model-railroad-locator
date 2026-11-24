<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class FollowDigestNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, Post>  $posts
     */
    public function __construct(public Collection $posts) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $count = $this->posts->count();

        return [
            'title' => $count.' new '.Str::plural('story', $count).' from railroads you follow',
            'message' => $this->posts->take(3)->pluck('title')->implode(', '),
            'url' => route('posts.index'),
            'icon' => 'digest',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->posts->count();

        $message = (new MailMessage)
            ->subject($count.' new '.Str::plural('story', $count).' from railroads you follow')
            ->greeting('Hello '.$notifiable->displayName().',')
            ->line('Here is what happened on the railroads and clubs you follow:');

        foreach ($this->posts->take(10) as $post) {
            $author = $post->author?->displayName() ?? 'A member';
            $layout = $post->layout ? ' on '.$post->layout->name : '';

            $message->line("**{$post->title}** — {$author}{$layout}");
        }

        $message
            ->action('Browse all stories', route('posts.index'))
            ->line('[Unsubscribe from weekly digests]('.URL::signedRoute('notifications.unsubscribe', [
                'user' => $notifiable,
                'type' => 'follow_digest',
            ]).')');

        return $message;
    }
}
