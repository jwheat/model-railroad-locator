<x-layouts.app>
    <section class="relative overflow-hidden rounded-3xl bg-emerald-950 px-6 py-14 text-stone-100 sm:px-10 lg:px-14">
        <div class="absolute inset-0 opacity-20"
             style="background-image: repeating-linear-gradient(90deg, transparent 0 44px, rgba(255,255,255,.25) 44px 45px), repeating-linear-gradient(0deg, transparent 0 56px, rgba(255,255,255,.18) 56px 58px);"></div>
        <div class="relative max-w-3xl">
            <p class="text-sm font-semibold tracking-[0.2em] text-amber-400 uppercase">The hobby, mapped</p>
            <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                Find the model railroads you've only heard about.
            </h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-emerald-100/90">
                Model Railroad Locator is a free directory of layouts and the people who build them.
                Follow club build reports, browse layouts near you, and share your own progress.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('layouts.index') }}"
                   class="rounded-xl bg-amber-500 px-5 py-3 text-sm font-semibold text-emerald-950 transition hover:bg-amber-400">
                    Browse layouts
                </a>
                @guest
                    <a href="{{ route('register') }}"
                       class="rounded-xl border border-white/25 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Start your free blog
                    </a>
                @else
                    <a href="{{ route('posts.create') }}"
                       class="rounded-xl border border-white/25 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Write a build report
                    </a>
                @endguest
            </div>
            <dl class="mt-10 grid max-w-lg grid-cols-3 gap-6 border-t border-white/10 pt-6 text-sm">
                <div>
                    <dt class="text-emerald-200/70">Layouts</dt>
                    <dd class="text-2xl font-semibold text-white">{{ number_format($layoutCount) }}</dd>
                </div>
                <div>
                    <dt class="text-emerald-200/70">Stories</dt>
                    <dd class="text-2xl font-semibold text-white">{{ number_format($postCount) }}</dd>
                </div>
                <div>
                    <dt class="text-emerald-200/70">Members</dt>
                    <dd class="text-2xl font-semibold text-white">{{ number_format($memberCount) }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <section class="mt-14">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Featured layouts</h2>
                <p class="mt-1 text-sm text-stone-500">Basements, barns, and club rooms worth a road trip.</p>
            </div>
            <a href="{{ route('layouts.index') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                See all layouts &rarr;
            </a>
        </div>

        @if ($featuredLayouts->isEmpty())
            <p class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                No layouts have been added yet. Be the first to put your railroad on the map.
            </p>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredLayouts as $layout)
                    <x-layout-card :layout="$layout" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-14">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Latest from the workbench</h2>
                <p class="mt-1 text-sm text-stone-500">Build reports, scenery experiments, and operating sessions.</p>
            </div>
            <a href="{{ route('posts.index') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-600">
                Read the blog &rarr;
            </a>
        </div>

        @if ($latestPosts->isEmpty())
            <p class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                No stories yet. Once members start posting, their build reports will show up here.
            </p>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($latestPosts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-16 flex flex-col items-start justify-between gap-6 rounded-3xl border border-amber-200 bg-amber-50 px-6 py-8 sm:flex-row sm:items-center sm:px-10">
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-emerald-950">Building something worth sharing?</h2>
            <p class="mt-1 text-sm text-emerald-900/70">
                Post photos, videos, and progress notes. Your layout page becomes your club's home on the web.
            </p>
        </div>
        <a href="{{ auth()->check() ? route('posts.create') : route('register') }}"
           class="rounded-xl bg-emerald-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">
            {{ auth()->check() ? 'Write a story' : 'Create your free account' }}
        </a>
    </section>
</x-layouts.app>
