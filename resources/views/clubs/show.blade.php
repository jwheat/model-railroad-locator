<x-layouts.app :title="$club->name">
    <header class="flex flex-col gap-6 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:p-8">
        @if ($club->logoUrl())
            <img src="{{ $club->logoUrl() }}" alt="{{ $club->name }}" class="h-20 w-20 rounded-2xl border border-stone-200 object-cover">
        @else
            <span class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-900 text-2xl font-bold text-amber-300">
                {{ Str::upper(Str::substr($club->name, 0, 2)) }}
            </span>
        @endif
        <div class="flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-3xl font-semibold tracking-tight text-stone-900">{{ $club->name }}</h1>
                @if ($club->is_verified)
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Verified club</span>
                @endif
            </div>
            <p class="mt-1 text-sm text-stone-500">
                {{ collect([$club->city, $club->region, $club->country])->filter()->implode(', ') ?: 'Location not shared' }}
                @if ($club->website)
                    <span class="mx-1">&middot;</span>
                    <a href="{{ $club->website }}" target="_blank" rel="noopener" class="text-emerald-800 hover:underline">Club website</a>
                @endif
            </p>
            @if ($club->description)
                <p class="mt-4 max-w-2xl leading-7 text-stone-700">{{ $club->description }}</p>
            @endif
        </div>
        <div class="shrink-0 rounded-2xl bg-stone-100 px-5 py-4 text-center">
            <p class="text-2xl font-semibold text-stone-900">{{ $club->members->count() }}</p>
            <p class="text-sm text-stone-500">{{ Str::plural('member', $club->members->count()) }}</p>
        </div>
    </header>

    <section class="mt-12">
        <h2 class="text-xl font-semibold tracking-tight text-stone-900">Layouts</h2>
        @if ($layouts->isEmpty())
            <p class="mt-4 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                This club hasn't published any layouts yet.
            </p>
        @else
            <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($layouts as $layout)
                    <x-layout-card :layout="$layout" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-12">
        <h2 class="text-xl font-semibold tracking-tight text-stone-900">Club stories</h2>
        @if ($posts->isEmpty())
            <p class="mt-4 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                No club stories yet.
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
        <h2 class="text-xl font-semibold tracking-tight text-stone-900">Members</h2>
        <div class="mt-4 flex flex-wrap gap-3">
            @foreach ($club->members as $member)
                <a href="{{ route('members.show', $member) }}"
                   class="flex items-center gap-2 rounded-full border border-stone-200 bg-white py-1 pr-4 pl-1 hover:border-emerald-300">
                    <x-avatar :user="$member" size="sm" class="!ring-stone-200" />
                    <span class="text-sm font-medium text-stone-800">{{ $member->displayName() }}</span>
                </a>
            @endforeach
        </div>
    </section>
</x-layouts.app>
