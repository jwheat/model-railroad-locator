@props(['layout'])

@php
    $gradients = [
        'Z' => 'from-emerald-900 via-emerald-800 to-teal-700',
        'N' => 'from-slate-900 via-slate-800 to-stone-700',
        'TT' => 'from-teal-900 via-teal-800 to-cyan-700',
        'HO' => 'from-amber-900 via-orange-800 to-amber-700',
        'OO' => 'from-rose-900 via-rose-800 to-orange-700',
        'S' => 'from-indigo-900 via-indigo-800 to-violet-700',
        'O' => 'from-stone-900 via-stone-800 to-neutral-700',
        'G' => 'from-lime-900 via-green-800 to-emerald-700',
    ];
    $gradient = $gradients[$layout->scale->value] ?? 'from-stone-900 via-stone-800 to-stone-700';
@endphp

<a href="{{ route('layouts.show', $layout) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
    <div class="relative h-40 w-full overflow-hidden bg-gradient-to-br {{ $gradient }}">
        @if ($layout->coverUrl())
            <img src="{{ $layout->coverUrl() }}" alt="{{ $layout->name }}"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="absolute inset-0 opacity-30"
                 style="background-image: repeating-linear-gradient(90deg, transparent 0 18px, rgba(255,255,255,.35) 18px 19px), repeating-linear-gradient(0deg, transparent 0 26px, rgba(255,255,255,.25) 26px 28px);"></div>
            <div class="absolute inset-x-0 bottom-0 flex items-end justify-between p-4">
                <span class="text-3xl font-black tracking-tight text-white/80">{{ $layout->scale->value }}</span>
                <span class="rounded-full bg-black/30 px-2.5 py-1 text-xs font-medium text-white/90 backdrop-blur">
                    {{ $layout->posts_count }} {{ Str::plural('story', $layout->posts_count) }}
                </span>
            </div>
        @endif
        <span class="absolute top-3 left-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-bold tracking-wide text-emerald-950 shadow-sm">
            {{ $layout->scale->value }}
        </span>
        @if ($layout->is_featured)
            <span class="absolute top-3 right-3 rounded-full bg-amber-400 px-2.5 py-1 text-xs font-semibold text-emerald-950 shadow-sm">
                Featured
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-2 p-5">
        <h3 class="text-lg font-semibold tracking-tight text-stone-900 group-hover:text-emerald-800">
            {{ $layout->name }}
        </h3>
        <p class="text-sm text-stone-500">
            @if ($layout->city || $layout->region)
                {{ collect([$layout->city, $layout->region])->filter()->implode(', ') }}
            @else
                Location not shared
            @endif
        </p>
        <p class="mt-auto pt-3 text-sm text-stone-600">
            by <span class="font-medium text-stone-800">{{ $layout->owner->displayName() }}</span>
        </p>
    </div>
</a>
