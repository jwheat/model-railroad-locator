<?php

namespace App\Livewire;

use Livewire\Attributes\Layout as LayoutAttribute;
use Livewire\Component;

#[LayoutAttribute('components.layouts.app')]
class NotificationPreferences extends Component
{
    public bool $comments = true;

    public bool $replies = true;

    public bool $digest = true;

    public function mount(): void
    {
        $user = auth()->user();

        $this->comments = $user->wantsNotification('comments');
        $this->replies = $user->wantsNotification('replies');
        $this->digest = $user->wantsNotification('follow_digest');
    }

    public function save(): void
    {
        auth()->user()->forceFill([
            'notify_comments' => $this->comments,
            'notify_replies' => $this->replies,
            'notify_follow_digest' => $this->digest,
        ])->save();

        session()->flash('status', 'Notification preferences saved.');
    }

    public function render()
    {
        return view('livewire.notification-preferences');
    }
}
