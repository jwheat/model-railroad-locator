<div>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-stone-900">Notifications</h1>
            <p class="mt-1 text-sm text-stone-500">Comments, replies, and new stories from the railroads you follow.</p>
        </div>
        <a href="{{ route('notifications.preferences') }}"
           class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">
            Email preferences
        </a>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        @forelse ($notifications as $notification)
            <a href="{{ data_get($notification->data, 'url', route('notifications.index')) }}"
               class="flex gap-4 border-b border-stone-100 px-5 py-4 transition last:border-b-0 hover:bg-stone-50">
                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ data_get($notification->data, 'icon') === 'digest' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                    @if (data_get($notification->data, 'icon') === 'digest')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                            <path d="M4 5h16v14H4zM4 9h16M8 13h8M8 17h5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @endif
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-semibold text-stone-900">{{ data_get($notification->data, 'title', 'Notification') }}</span>
                    <span class="mt-0.5 block text-sm text-stone-600">{{ data_get($notification->data, 'message', '') }}</span>
                    <span class="mt-1 block text-xs text-stone-400">{{ $notification->created_at->diffForHumans() }}</span>
                </span>
                @if (! $notification->read_at)
                    <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-emerald-500" aria-label="Unread"></span>
                @endif
            </a>
        @empty
            <p class="px-5 py-12 text-center text-sm text-stone-500">
                Nothing yet. Follow a layout, club, or member and their new stories will show up here.
            </p>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
</div>
