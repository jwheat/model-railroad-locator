<div>
    @if (! $isSelf)
        <div class="flex flex-wrap items-center gap-2">
            @auth
                <button type="button" wire:click="toggle" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition disabled:opacity-50 {{ $following
                            ? 'border border-emerald-300 bg-emerald-50 text-emerald-900 hover:bg-emerald-100'
                            : 'bg-emerald-900 text-white hover:bg-emerald-800' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ $following ? 'Following' : 'Follow' }} {{ $label }}
                </button>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Follow {{ $label }}
                </a>
            @endauth

            <span class="text-xs text-stone-500">{{ $count }} {{ Str::plural('follower', $count) }}</span>
        </div>
    @endif
</div>
