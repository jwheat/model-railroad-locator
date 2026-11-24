<?php

namespace App\Livewire;

use Livewire\Attributes\Layout as LayoutAttribute;
use Livewire\Component;
use Livewire\WithPagination;

#[LayoutAttribute('components.layouts.app')]
class NotificationInbox extends Component
{
    use WithPagination;

    public function mount(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.notification-inbox', [
            'notifications' => auth()->user()->notifications()->latest()->paginate(20),
        ]);
    }
}
