<x-layouts.app title="Stories">
    <header class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold tracking-[0.18em] text-amber-700 uppercase">Member blogs</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">Build reports &amp; stories</h1>
            <p class="mt-3 text-stone-600">
                Progress notes, scenery experiments, operating sessions, and the occasional derailment.
            </p>
        </div>

        <form method="GET" action="{{ route('posts.index') }}" class="flex w-full max-w-md gap-2">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search stories..."
                   class="w-full rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
            <button type="submit" class="rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
                Search
            </button>
        </form>
    </header>

    @if ($search !== '')
        <p class="mt-6 text-sm text-stone-500">
            {{ $posts->total() }} {{ Str::plural('result', $posts->total()) }} for
            <span class="font-semibold text-stone-800">&ldquo;{{ $search }}&rdquo;</span>
            &middot; <a href="{{ route('posts.index') }}" class="text-emerald-800 hover:underline">clear</a>
        </p>
    @endif

    @if ($posts->isEmpty())
        <div class="mt-10 rounded-2xl border border-dashed border-stone-300 bg-white p-10 text-center">
            <p class="text-stone-600">No stories match that search yet.</p>
            @auth
                <a href="{{ route('posts.create') }}" class="mt-4 inline-block rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
                    Write the first one
                </a>
            @endauth
        </div>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <x-post-card :post="$post" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
</x-layouts.app>
