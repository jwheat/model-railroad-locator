<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false" wire:poll.60s>
    <button type="button" x-on:click="open = !open"
            class="relative flex h-9 w-9 items-center justify-center rounded-lg text-stone-300 transition hover:bg-white/10 hover:text-white"
            aria-label="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold text-emerald-950">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak
         class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-2xl border border-stone-200 bg-white text-stone-900 shadow-xl">
        <div class="flex items-center justify-between gap-3 border-b border-stone-100 px-4 py-3">
            <p class="text-sm font-semibold">Notifications</p>
            @if ($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="text-xs font-semibold text-emerald-800 hover:text-emerald-600">
                    Mark all read
                </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse ($notifications as $notification)
                <a href="{{ data_get($notification->data, 'url', route('notifications.index')) }}"
                   class="block border-b border-stone-100 px-4 py-3 transition hover:bg-stone-50 {{ $notification->read_at ? '' : 'bg-emerald-50/60' }}">
                    <p class="text-sm font-semibold text-stone-900">{{ data_get($notification->data, 'title', 'Notification') }}</p>
                    <p class="mt-0.5 line-clamp-2 text-xs text-stone-600">{{ data_get($notification->data, 'message', '') }}</p>
                    <p class="mt-1 text-xs text-stone-400">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <p class="px-4 py-8 text-center text-sm text-stone-500">You're all caught up.</p>
            @endforelse
        </div>

        <div class="flex items-center justify-between gap-3 bg-stone-50 px-4 py-2.5 text-xs font-semibold">
            <a href="{{ route('notifications.index') }}" class="text-emerald-800 hover:text-emerald-600">View all</a>
            <a href="{{ route('notifications.preferences') }}" class="text-stone-500 hover:text-stone-800">Preferences</a>
        </div>
    </div>
</div>
