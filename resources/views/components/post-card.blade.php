@props(['post', 'featured' => false])

<article class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:border-emerald-300 hover:shadow-md">
    @if ($featured)
        <a href="{{ route('posts.show', $post) }}" class="relative block h-56 overflow-hidden bg-emerald-950">
            @if ($post->coverUrl())
                <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}"
                     class="h-full w-full object-cover opacity-90 transition duration-500 group-hover:scale-105">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-emerald-900 to-amber-900"></div>
                <div class="absolute inset-0 opacity-20"
                     style="background-image: repeating-linear-gradient(90deg, transparent 0 26px, rgba(255,255,255,.35) 26px 27px), repeating-linear-gradient(0deg, transparent 0 34px, rgba(255,255,255,.25) 34px 36px);"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-5xl font-black tracking-tight text-white/15">{{ $post->layout?->scale->value ?? 'MRL' }}</span>
                </div>
            @endif
        </a>
    @endif

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-stone-500">
            @if ($post->layout)
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-800">{{ $post->layout->name }}</span>
            @endif
            <time datetime="{{ $post->published_at?->toIso8601String() }}">
                {{ ($post->published_at ?? $post->created_at)->format('M j, Y') }}
            </time>
            <span>&middot;</span>
            <span>{{ $post->readingMinutes() }} min read</span>
        </div>

        <h3 class="text-lg font-semibold tracking-tight text-stone-900">
            <a href="{{ route('posts.show', $post) }}" class="group-hover:text-emerald-800">{{ $post->title }}</a>
        </h3>

        <p class="text-sm leading-6 text-stone-600">{{ $post->excerptText() }}</p>

        <div class="mt-auto flex items-center gap-3 pt-3">
            <x-avatar :user="$post->author" size="sm" class="!ring-stone-200" />
            <div class="text-sm">
                <a href="{{ route('members.show', $post->author) }}" class="font-medium text-stone-800 hover:text-emerald-800">
                    {{ $post->author->displayName() }}
                </a>
                <p class="text-xs text-stone-500">{{ $post->comments_count ?? 0 }} {{ Str::plural('comment', $post->comments_count ?? 0) }}</p>
            </div>
        </div>
    </div>
</article>
