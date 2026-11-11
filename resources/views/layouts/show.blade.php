<x-layouts.app :title="$layout->name">
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

    <nav class="text-sm text-stone-500">
        <a href="{{ route('layouts.index') }}" class="hover:text-emerald-800">Layouts</a>
        <span class="mx-2">/</span>
        <span class="text-stone-700">{{ $layout->name }}</span>
    </nav>

    <header class="mt-4 overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm">
        <div class="relative h-64 w-full bg-gradient-to-br {{ $gradient }} sm:h-80">
            @if ($layout->coverUrl())
                <img src="{{ $layout->coverUrl() }}" alt="{{ $layout->name }}" class="h-full w-full object-cover">
            @else
                <div class="absolute inset-0 opacity-25"
                     style="background-image: repeating-linear-gradient(90deg, transparent 0 34px, rgba(255,255,255,.4) 34px 35px), repeating-linear-gradient(0deg, transparent 0 44px, rgba(255,255,255,.3) 44px 46px);"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-8xl font-black tracking-tight text-white/15">{{ $layout->scale->value }}</span>
                </div>
            @endif
            <span class="absolute top-5 left-5 rounded-full bg-white/95 px-3 py-1.5 text-sm font-bold text-emerald-950">
                {{ $layout->scale->label() }}
            </span>
            @if ($layout->is_featured)
                <span class="absolute top-5 right-5 rounded-full bg-amber-400 px-3 py-1.5 text-sm font-semibold text-emerald-950">
                    Featured layout
                </span>
            @endif
        </div>

        <div class="flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">{{ $layout->name }}</h1>
                <p class="mt-2 text-stone-500">
                    @if ($layout->city || $layout->region || $layout->country)
                        {{ collect([$layout->city, $layout->region, $layout->country])->filter()->implode(', ') }}
                    @else
                        Location not shared
                    @endif
                    @if ($layout->era)
                        <span class="mx-2">&middot;</span>{{ $layout->era }}
                    @endif
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                    <a href="{{ route('members.show', $layout->owner) }}" class="flex items-center gap-2 rounded-full border border-stone-200 py-1 pr-3 pl-1 hover:border-emerald-300">
                        <x-avatar :user="$layout->owner" size="sm" class="!ring-stone-200" />
                        <span class="font-medium text-stone-800">{{ $layout->owner->displayName() }}</span>
                    </a>
                    @if ($layout->club)
                        <a href="{{ route('clubs.show', $layout->club) }}"
                           class="rounded-full border border-stone-200 px-3 py-1.5 font-medium text-stone-700 hover:border-emerald-300">
                            {{ $layout->club->name }}
                        </a>
                    @endif
                    @foreach ($layout->tags as $tag)
                        <span class="rounded-full bg-stone-100 px-3 py-1.5 text-stone-600">#{{ $tag->name }}</span>
                    @endforeach
                </div>

                @if ($layout->description)
                    <p class="mt-6 leading-7 text-stone-700">{{ $layout->description }}</p>
                @endif
            </div>

            <div class="flex shrink-0 flex-col gap-4 lg:w-64">
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-2xl font-semibold text-stone-900">{{ $layout->posts_count }}</p>
                    <p class="text-sm text-stone-500">{{ Str::plural('build report', $layout->posts_count) }} published</p>
                </div>
                @if ($layout->latitude && $layout->longitude)
                    <a href="https://www.openstreetmap.org/?mlat={{ $layout->latitude }}&mlon={{ $layout->longitude }}#map=13/{{ $layout->latitude }}/{{ $layout->longitude }}"
                       target="_blank" rel="noopener"
                       class="rounded-xl border border-stone-200 px-4 py-3 text-center text-sm font-semibold text-emerald-800 hover:bg-emerald-50">
                        View on a map
                    </a>
                @endif
                @can('update', $layout)
                    <a href="{{ route('layouts.edit', $layout) }}"
                       class="rounded-xl border border-stone-200 px-4 py-3 text-center text-sm font-semibold text-stone-700 hover:bg-stone-50">
                        Edit layout
                    </a>
                @endcan
            </div>
        </div>
    </header>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-stone-200 bg-white px-5 py-4">
        <livewire:reaction-bar reactionable-type="layout" :reactionable-id="$layout->id" />
        <p class="text-sm text-stone-500">Share this layout with your club.</p>
    </div>

    <section class="mt-12">
        <div class="flex items-end justify-between gap-4">
            <h2 class="text-xl font-semibold tracking-tight text-stone-900">Build reports</h2>
            @auth
                <a href="{{ route('posts.create') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-600">Write an update &rarr;</a>
            @endauth
        </div>

        @if ($posts->isEmpty())
            <p class="mt-4 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                No stories have been posted about this layout yet.
            </p>
        @else
            <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-12">
        <h2 class="text-xl font-semibold tracking-tight text-stone-900">Comments</h2>
        <livewire:comments commentable-type="layout" :commentable-id="$layout->id" />
    </section>

    @if ($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-xl font-semibold tracking-tight text-stone-900">More {{ $layout->scale->value }} layouts</h2>
            <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $relatedLayout)
                    <x-layout-card :layout="$relatedLayout" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
