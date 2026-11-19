@props(['photos' => [], 'gallery' => null])

@php
    $gallery = $gallery ?? collect();
    $total = $gallery->count() + count($photos);
@endphp

<div>
    <div class="flex items-baseline justify-between gap-3">
        <label class="block text-sm font-medium text-stone-700">More photos <span class="text-stone-400">(optional)</span></label>
        <span class="text-xs text-stone-400">{{ $total }} / 8</span>
    </div>

    @if ($total > 0)
        <div class="mt-2 grid grid-cols-3 gap-2 sm:grid-cols-4">
            @foreach ($gallery as $photo)
                <div wire:key="photo-{{ $photo->id }}" class="group relative overflow-hidden rounded-xl border border-stone-200 bg-stone-100">
                    <img src="{{ $photo->publicUrl() }}" alt="{{ $photo->alt ?: 'Photo' }}"
                         class="aspect-square w-full object-cover">
                    <button type="button" wire:click="removePhoto({{ $photo->id }})"
                            wire:loading.attr="disabled"
                            class="absolute top-1.5 right-1.5 rounded-full bg-stone-900/70 p-1 text-white opacity-90 transition hover:bg-rose-700 focus:opacity-100"
                            aria-label="Remove photo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                    </button>
                </div>
            @endforeach

            @foreach ($photos as $index => $photo)
                <div wire:key="new-photo-{{ $photo->getFilename() }}" class="group relative overflow-hidden rounded-xl border border-dashed border-emerald-300 bg-stone-100">
                    <img src="{{ $photo->temporaryUrl() }}" alt="New photo"
                         class="aspect-square w-full object-cover">
                    <button type="button" wire:click="removeNewPhoto({{ $index }})"
                            wire:loading.attr="disabled"
                            class="absolute top-1.5 right-1.5 rounded-full bg-stone-900/70 p-1 text-white opacity-90 transition hover:bg-rose-700 focus:opacity-100"
                            aria-label="Remove photo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    @if ($total < 8)
        <label x-data="{
                   dragging: false,
                   upload(files) {
                       // Livewire sends a multi-file selection in one request. Upload one at a
                       // time so the request stays under the server's post_max_size limit.
                       Array.from(files).forEach((file) => this.$wire.uploadMultiple('photos', [file]));
                   },
               }"
               x-on:dragover.prevent="dragging = true"
               x-on:dragleave.prevent="dragging = false"
               x-on:drop.prevent="
                   dragging = false;
                   if ($event.dataTransfer.files.length) {
                       upload($event.dataTransfer.files);
                   }
               "
               x-bind:class="dragging ? 'border-emerald-500 bg-emerald-50' : 'border-stone-300 bg-stone-50 hover:border-emerald-400'"
               class="mt-3 flex cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed px-4 py-5 text-center transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-stone-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <span class="text-sm font-medium text-stone-700">
                {{ $total > 0 ? 'Add more photos' : 'Drag photos here or click to browse' }}
            </span>
            <span class="text-xs text-stone-500">JPG, PNG, WebP or GIF &middot; up to 5 MB each</span>
            <input type="file" multiple accept="image/*" class="sr-only"
                   x-on:change="
                       if ($event.target.files.length) {
                           upload($event.target.files);
                       }
                       $event.target.value = '';
                   ">
        </label>
        <p wire:loading wire:target="photos" class="mt-1 text-xs text-stone-500">Uploading...</p>
    @endif

    @error('photos')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
    @foreach ($errors->get('photos.*') as $message)
        <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
    @endforeach
</div>
