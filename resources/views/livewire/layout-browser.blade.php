<div>
    <div id="near-me" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="inline-flex rounded-xl border border-stone-300 bg-stone-100 p-1" role="group" aria-label="Directory view">
                <button type="button" wire:click="setViewMode('map')" aria-pressed="{{ $viewMode === 'map' ? 'true' : 'false' }}"
                        class="rounded-lg px-4 py-1.5 text-sm font-semibold transition {{ $viewMode === 'map' ? 'bg-white text-emerald-900 shadow-sm' : 'text-stone-600 hover:text-stone-900' }}">
                    Map
                </button>
                <button type="button" wire:click="setViewMode('list')" aria-pressed="{{ $viewMode === 'list' ? 'true' : 'false' }}"
                        class="rounded-lg px-4 py-1.5 text-sm font-semibold transition {{ $viewMode === 'list' ? 'bg-white text-emerald-900 shadow-sm' : 'text-stone-600 hover:text-stone-900' }}">
                    List
                </button>
            </div>

            <div class="flex items-center gap-2 text-sm">
                <span class="text-stone-500">Distances in</span>
                <div class="inline-flex rounded-xl border border-stone-300 bg-white p-1" role="group" aria-label="Distance unit">
                    <button type="button" wire:click="setDistanceUnit('mi')" aria-pressed="{{ $distanceUnit === 'mi' ? 'true' : 'false' }}"
                            class="rounded-lg px-3 py-1 font-semibold transition {{ $distanceUnit === 'mi' ? 'bg-emerald-900 text-white' : 'text-stone-600 hover:text-stone-900' }}">
                        Miles
                    </button>
                    <button type="button" wire:click="setDistanceUnit('km')" aria-pressed="{{ $distanceUnit === 'km' ? 'true' : 'false' }}"
                            class="rounded-lg px-3 py-1 font-semibold transition {{ $distanceUnit === 'km' ? 'bg-emerald-900 text-white' : 'text-stone-600 hover:text-stone-900' }}">
                        Kilometers
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4 grid gap-3 lg:grid-cols-[1fr_auto_auto_auto]">
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
                        x-on:click="window.mrlUseMyLocation($wire)"
                        class="w-full rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold whitespace-nowrap text-white hover:bg-emerald-800">
                    Use my location
                </button>
            </div>
        </div>

        <p id="layout-location-status" wire:ignore class="location-status mt-3 hidden text-sm" role="status" aria-live="polite"></p>

        @if ($latitude !== null && $longitude !== null)
            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                <span class="rounded-full bg-emerald-50 px-3 py-1 font-medium text-emerald-800">
                    Sorted by distance from your location
                </span>
                <label class="flex items-center gap-2 text-stone-600">
                    Within
                    <select wire:model.live="radius"
                            class="rounded-lg border border-stone-300 bg-white px-2 py-1 text-sm">
                        @foreach ($radiusOptions as $option)
                            <option value="{{ $option }}">{{ $option }} {{ $distanceUnit }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="button" wire:click="clearLocation" class="font-medium text-stone-500 hover:text-stone-800">
                    Clear location
                </button>
            </div>
        @endif
    </div>

    <div wire:loading.delay class="mt-4 text-sm text-stone-500">Updating results...</div>

    <section class="{{ $viewMode === 'map' ? '' : 'hidden' }}" aria-label="Layout map">
        <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-200 px-4 py-3 text-sm">
                <p class="font-medium text-stone-700">
                    {{ count($mapLayouts) }} {{ Str::plural('layout', count($mapLayouts)) }} on the map
                </p>
                <p class="text-stone-500">Drag and zoom to explore. We'll center on your area when we can.</p>
            </div>

            <div x-data
                 x-on:layout-map-bounds.window="$wire.setMapBounds($event.detail.north, $event.detail.south, $event.detail.east, $event.detail.west)">
                <div id="layout-map"
                     wire:ignore
                     class="h-[28rem] w-full sm:h-[34rem]"
                     role="region"
                     aria-label="Map of model railroad layouts"
                     data-layouts="{{ e(json_encode($mapLayouts)) }}"
                     data-unit="{{ $distanceUnit }}"
                     data-user-location="{{ $latitude !== null && $longitude !== null ? e(json_encode(['latitude' => $latitude, 'longitude' => $longitude])) : '' }}"></div>
            </div>

            @if (count($mapLayouts) === 0)
                <p class="border-t border-stone-200 px-4 py-3 text-sm text-stone-500">
                    No layouts with coordinates match those filters yet.
                </p>
            @endif

            @if ($unmappedCount > 0)
                <p class="border-t border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-500">
                    {{ $unmappedCount }} {{ Str::plural('layout', $unmappedCount) }} {{ $unmappedCount === 1 ? 'is' : 'are' }} listed without coordinates, so {{ $unmappedCount === 1 ? 'it does' : 'they do' }} not appear on the map.
                    @auth
                        <a href="{{ route('layouts.create') }}" class="font-medium text-emerald-800 hover:text-emerald-600">Add coordinates</a>
                    @endauth
                </p>
            @endif
        </div>
    </section>

    <section class="{{ $viewMode === 'list' ? '' : 'hidden' }}" aria-label="Layout list">
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
                        <x-layout-card :layout="$layout" :unit="$distanceUnit" />
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $layouts->links() }}
            </div>
        @endif
    </section>
</div>
