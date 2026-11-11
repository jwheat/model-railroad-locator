<x-layouts.app title="Browse layouts">
    <header class="max-w-2xl">
        <p class="text-sm font-semibold tracking-[0.18em] text-amber-700 uppercase">Layout directory</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">Find a railroad near you</h1>
        <p class="mt-3 text-stone-600">
            Search by name, city, era, or scale. Turn on your location to sort by distance and plan your next layout visit.
        </p>
    </header>

    <div class="mt-8">
        <livewire:layout-browser />
    </div>
</x-layouts.app>
