<x-layouts.app :title="$member->displayName()">
    <header class="flex flex-col gap-6 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:p-8">
        <x-avatar :user="$member" size="lg" class="!ring-stone-200" />
        <div class="flex-1">
            <p class="text-sm font-medium text-stone-500">{{ '@'.$member->username }}</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-stone-900">{{ $member->displayName() }}</h1>
            <p class="mt-1 text-sm text-stone-500">
                @if ($member->city || $member->region)
                    {{ collect([$member->city, $member->region])->filter()->implode(', ') }}
                    <span class="mx-1">&middot;</span>
                @endif
                Member since {{ $member->created_at->format('M Y') }}
            </p>
            @if ($member->bio)
                <p class="mt-4 max-w-2xl leading-7 text-stone-700">{{ $member->bio }}</p>
            @endif
        </div>
        @if (auth()->id() === $member->id)
            <div class="flex flex-col gap-2">
                <a href="{{ route('posts.create') }}"
                   class="rounded-xl bg-emerald-900 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-emerald-800">
                    Write a story
                </a>
                <a href="{{ route('layouts.create') }}"
                   class="rounded-xl border border-stone-200 px-4 py-2.5 text-center text-sm font-semibold text-stone-700 hover:bg-stone-50">
                    Add a layout
                </a>
            </div>
        @endif
    </header>

    @if ($clubs->isNotEmpty())
        <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
            <span class="text-stone-500">Clubs:</span>
            @foreach ($clubs as $club)
                <a href="{{ route('clubs.show', $club) }}"
                   class="rounded-full border border-stone-200 bg-white px-3 py-1.5 font-medium text-stone-700 hover:border-emerald-300">
                    {{ $club->name }}
                </a>
            @endforeach
        </div>
    @endif

    <section class="mt-12">
        <div class="flex items-end justify-between gap-4">
            <h2 class="text-xl font-semibold tracking-tight text-stone-900">Layouts</h2>
            <span class="text-sm text-stone-500">{{ $layouts->count() }} listed</span>
        </div>

        @if ($layouts->isEmpty())
            <p class="mt-4 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                No public layouts yet.
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
        <div class="flex items-end justify-between gap-4">
            <h2 class="text-xl font-semibold tracking-tight text-stone-900">Stories</h2>
            <a href="{{ route('posts.index', ['q' => $member->displayName()]) }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                Search the blog &rarr;
            </a>
        </div>

        @if ($posts->isEmpty())
            <p class="mt-4 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                No published stories yet.
            </p>
        @else
            <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </section>
</x-layouts.app>
