<div>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold tracking-[0.18em] text-amber-700 uppercase">Workbench</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900">
                {{ $postId ? 'Edit story' : 'Write a story' }}
            </h1>
            <p class="mt-2 max-w-2xl text-sm text-stone-600">
                Markdown is supported. Raw HTML is stripped for safety. Add a cover photo and an optional video embed.
            </p>
        </div>
        <div class="flex gap-2">
            <button type="button" wire:click="saveDraft" wire:loading.attr="disabled"
                    class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50 disabled:opacity-50">
                Save draft
            </button>
            <button type="button" wire:click="publish" wire:loading.attr="disabled"
                    class="rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-50">
                Publish
            </button>
        </div>
    </div>

    @error('body')<p class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $message }}</p>@enderror

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_1fr]">
        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div>
                <label for="title" class="block text-sm font-medium text-stone-700">Title</label>
                <input id="title" type="text" wire:model="title"
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                @error('title')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-stone-700">Summary <span class="text-stone-400">(optional)</span></label>
                <textarea id="excerpt" wire:model="excerpt" rows="2"
                          class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"></textarea>
                @error('excerpt')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="body" class="block text-sm font-medium text-stone-700">Story</label>
                <textarea id="body" wire:model.live.debounce.700ms="body" rows="16"
                          placeholder="## This week on the layout&#10;&#10;We finally finished the yard throat..."
                          class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-3 font-mono text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"></textarea>
                @error('body')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="layoutId" class="block text-sm font-medium text-stone-700">Layout</label>
                    <select id="layoutId" wire:model="layoutId"
                            class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                        <option value="">No layout</option>
                        @foreach ($layouts as $layout)
                            <option value="{{ $layout->id }}">{{ $layout->name }}</option>
                        @endforeach
                    </select>
                    @error('layoutId')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="clubId" class="block text-sm font-medium text-stone-700">Club</label>
                    <select id="clubId" wire:model="clubId"
                            class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                        <option value="">No club</option>
                        @foreach ($clubs as $club)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endforeach
                    </select>
                    @error('clubId')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-stone-700">Cover photo</label>
                <input type="file" wire:model="cover" accept="image/*"
                       class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm">
                <p wire:loading wire:target="cover" class="mt-1 text-xs text-stone-500">Uploading...</p>
                @error('cover')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="videoUrl" class="block text-sm font-medium text-stone-700">Video embed URL <span class="text-stone-400">(optional)</span></label>
                <input id="videoUrl" type="url" wire:model="videoUrl" placeholder="https://www.youtube.com/embed/..."
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                <p class="mt-1 text-xs text-stone-500">Use the embed URL from YouTube or Vimeo. Large uploads should live on a video host.</p>
                @error('videoUrl')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:sticky lg:top-6 lg:self-start">
            <p class="text-xs font-semibold tracking-[0.18em] text-stone-400 uppercase">Live preview</p>
            <div class="prose-rail mt-4 max-h-[42rem] overflow-y-auto pr-2">
                @if (trim($body) === '')
                    <p class="text-sm text-stone-400">Your story preview will appear here as you type.</p>
                @else
                    {!! $preview !!}
                @endif
            </div>
        </div>
    </div>
</div>
