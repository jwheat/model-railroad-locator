<x-layouts.app :title="$post->title" :description="$post->excerptText(150)">
    <nav class="mx-auto max-w-4xl text-sm text-stone-500">
        <a href="{{ route('posts.index') }}" class="hover:text-emerald-800">Stories</a>
        <span class="mx-2">/</span>
        <span class="text-stone-700">{{ $post->title }}</span>
    </nav>

    <article class="mx-auto mt-6 max-w-4xl">
        <header>
            <div class="flex flex-wrap items-center gap-2 text-sm text-stone-500">
                @if ($post->layout)
                    <a href="{{ route('layouts.show', $post->layout) }}"
                       class="rounded-full bg-emerald-50 px-3 py-1 font-medium text-emerald-800 hover:bg-emerald-100">
                        {{ $post->layout->name }}
                    </a>
                @endif
                @if ($post->club)
                    <a href="{{ route('clubs.show', $post->club) }}" class="rounded-full bg-stone-100 px-3 py-1 hover:bg-stone-200">
                        {{ $post->club->name }}
                    </a>
                @endif
                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ ($post->published_at ?? $post->created_at)->format('F j, Y') }}
                </time>
                <span>&middot;</span>
                <span>{{ $post->readingMinutes() }} min read</span>
            </div>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">{{ $post->title }}</h1>

            @if ($post->excerpt)
                <p class="mt-4 text-lg leading-8 text-stone-600">{{ $post->excerpt }}</p>
            @endif

            <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-y border-stone-200 py-4">
                <a href="{{ route('members.show', $post->author) }}" class="flex items-center gap-3">
                    <x-avatar :user="$post->author" class="!ring-stone-200" />
                    <span>
                        <span class="block text-sm font-semibold text-stone-900">{{ $post->author->displayName() }}</span>
                        <span class="block text-xs text-stone-500">{{ '@'.$post->author->username }}</span>
                    </span>
                </a>
                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}"
                       class="rounded-xl border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                        Edit story
                    </a>
                @endcan
            </div>
        </header>

        @if ($post->coverUrl())
            <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}"
                 class="mt-8 w-full rounded-2xl border border-stone-200 object-cover">
        @endif

        @if (! $post->isPublished())
            <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                This is a draft only you can see. Publish it when you're ready to share it.
            </p>
        @endif

        <div class="prose-rail mt-8">
            {!! Str::markdown($post->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>

        @if ($post->media->where('type', 'video_embed')->isNotEmpty())
            <div class="mt-8 space-y-4">
                @foreach ($post->media->where('type', 'video_embed') as $video)
                    <div class="aspect-video overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
                        <iframe src="{{ $video->url }}" title="Video for {{ $post->title }}" class="h-full w-full"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen loading="lazy"></iframe>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($post->tags->isNotEmpty())
            <div class="mt-8 flex flex-wrap gap-2">
                @foreach ($post->tags as $tag)
                    <span class="rounded-full bg-stone-100 px-3 py-1.5 text-sm text-stone-600">#{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-stone-200 bg-white px-5 py-4">
            <livewire:reaction-bar reactionable-type="post" :reactionable-id="$post->id" />
            <p class="text-sm text-stone-500">{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</p>
        </div>

        @if ($post->allow_comments)
            <section class="mt-10">
                <h2 class="text-xl font-semibold tracking-tight text-stone-900">Comments</h2>
                <livewire:comments commentable-type="post" :commentable-id="$post->id" />
            </section>
        @endif
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto mt-14 max-w-4xl">
            <h2 class="text-xl font-semibold tracking-tight text-stone-900">More from {{ $post->author->displayName() }}</h2>
            <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $relatedPost)
                    <x-post-card :post="$relatedPost" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
