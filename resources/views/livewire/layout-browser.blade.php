<div>
    <div id="near-me" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 lg:grid-cols-[1fr_auto_auto_auto]">
            <div class="relative">
                <input type="search" wire:model.live.debounce.400ms="search"
                       placeholder="Search by name, city, era..."
                       class="w-full rounded-xl border border-stone-300 py-2.5 pr-4 pl-10 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-stone-400">
                    <circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" stroke-linecap="round" />
                </svg>
            </div>

            <select wire:model.live="scale"
                    class="rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                <option value="">All scales</option>
                @foreach ($scales as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="sort"
                    class="rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                <option value="newest">Newest first</option>
                <option value="popular">Most stories</option>
                <option value="distance">Closest to me</option>
            </select>

            <div x-data class="flex gap-2">
                <button type="button"
                        x-on:click="navigator.geolocation.getCurrentPosition(
                            position => $wire.setCoordinates(position.coords.latitude, position.coords.longitude),
                            () => alert('We could not access your location. You can still search by city.')
                        )"
                        class="w-full rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold whitespace-nowrap text-white hover:bg-emerald-800">
                    Use my location
                </button>
            </div>
        </div>

        @if ($latitude !== null && $longitude !== null)
            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                <span class="rounded-full bg-emerald-50 px-3 py-1 font-medium text-emerald-800">
                    Sorted by distance from your location
                </span>
                <label class="flex items-center gap-2 text-stone-600">
                    Within
                    <select wire:model.live="radius"
                            class="rounded-lg border border-stone-300 bg-white px-2 py-1 text-sm">
                        <option value="25">25 km</option>
                        <option value="100">100 km</option>
                        <option value="250">250 km</option>
                        <option value="1000">1000 km</option>
                    </select>
                </label>
                <button type="button" wire:click="clearLocation" class="font-medium text-stone-500 hover:text-stone-800">
                    Clear location
                </button>
            </div>
        @endif
    </div>

    <div wire:loading.delay class="mt-4 text-sm text-stone-500">Updating results...</div>

    @if ($layouts->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-stone-300 bg-white p-10 text-center">
            <p class="text-stone-600">No layouts match those filters yet.</p>
            @auth
                <a href="{{ route('layouts.create') }}" class="mt-4 inline-block rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
                    Add your layout
                </a>
            @endauth
        </div>
    @else
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($layouts as $layout)
                <div wire:key="layout-{{ $layout->id }}">
                    <x-layout-card :layout="$layout" />
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $layouts->links() }}
        </div>
    @endif
</div>
