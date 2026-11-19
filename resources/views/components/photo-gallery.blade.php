@props(['photos', 'alt' => 'Photo', 'grid' => 'sm:grid-cols-2 lg:grid-cols-3'])

@php
    $photos = collect($photos);
@endphp

<div
    x-data="{
        open: false,
        src: null,
        alt: null,
        caption: null,
        show(photo) {
            this.src = photo.src;
            this.alt = photo.alt;
            this.caption = photo.caption;
            this.open = true;
            this.$nextTick(() => this.$refs.closeButton?.focus());
        },
        close() {
            this.open = false;
        },
    }"
    x-effect="document.body.style.overflow = open ? 'hidden' : ''"
    x-on:keydown.escape.window="close()"
>
    <div class="mt-4 grid gap-3 {{ $grid }}">
        @foreach ($photos as $photo)
            <figure class="overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
                <button type="button"
                        x-on:click="show(@js([
                            'src' => $photo->publicUrl(),
                            'alt' => $photo->alt ?: $alt,
                            'caption' => $photo->caption,
                        ]))"
                        class="group relative block w-full cursor-zoom-in overflow-hidden"
                        aria-label="View {{ $photo->alt ?: $alt }} full screen">
                    <img src="{{ $photo->publicUrl() }}" alt="{{ $photo->alt ?: $alt }}"
                         class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-[1.02] group-hover:brightness-95"
                         loading="lazy">
                    <span class="absolute inset-0 flex items-center justify-center bg-stone-950/0 transition group-hover:bg-stone-950/20">
                        <span class="rounded-full bg-white/90 p-2 text-stone-800 opacity-0 shadow-sm transition group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                            </svg>
                        </span>
                    </span>
                </button>
                @if ($photo->caption)
                    <figcaption class="px-4 py-2 text-sm text-stone-600">{{ $photo->caption }}</figcaption>
                @endif
            </figure>
        @endforeach
    </div>

    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-8"
         role="dialog" aria-modal="true" aria-label="Photo viewer">
        <div class="absolute inset-0 bg-stone-950/95" x-on:click="close()"></div>

        <figure class="relative flex max-h-full max-w-full flex-col items-center" x-on:click.stop>
            <img :src="src" :alt="alt"
                 class="max-h-[86vh] max-w-[94vw] rounded-xl object-contain shadow-2xl ring-1 ring-white/10">
            <figcaption class="mt-4 max-w-2xl text-center text-sm text-stone-200" x-show="caption" x-text="caption"></figcaption>
        </figure>

        <button type="button" x-on:click="close()" x-ref="closeButton"
                class="absolute top-4 right-4 rounded-full bg-white/10 p-2.5 text-white backdrop-blur transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50 sm:top-6 sm:right-6"
                aria-label="Close full screen photo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
            </svg>
        </button>
    </div>
</div>
