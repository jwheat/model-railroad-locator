<div>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold tracking-[0.18em] text-amber-700 uppercase">Layout directory</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900">
                {{ $layoutId ? 'Edit layout' : 'Add your layout' }}
            </h1>
            <p class="mt-2 max-w-2xl text-sm text-stone-600">
                Public layouts appear in search and on your profile. Unlisted layouts are visible by link only.
            </p>
        </div>
        <button type="button" wire:click="save" wire:loading.attr="disabled"
                class="rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-50">
            Save layout
        </button>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold tracking-tight text-stone-900">Layout details</h2>

            <div>
                <label for="name" class="block text-sm font-medium text-stone-700">Name</label>
                <input id="name" type="text" wire:model="name"
                       class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                @error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="scale" class="block text-sm font-medium text-stone-700">Scale</label>
                    <select id="scale" wire:model="scale"
                            class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                        @foreach ($scales as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('scale')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="era" class="block text-sm font-medium text-stone-700">Era <span class="text-stone-400">(optional)</span></label>
                    <input id="era" type="text" wire:model="era" placeholder="Transition, 1950s..."
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-stone-700">Description</label>
                <textarea id="description" wire:model="description" rows="5"
                          placeholder="Tell visitors what makes this railroad interesting..."
                          class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"></textarea>
                @error('description')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-stone-700">Cover photo</label>
                <input type="file" wire:model="cover" accept="image/*"
                       class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm">
                <p wire:loading wire:target="cover" class="mt-1 text-xs text-stone-500">Uploading...</p>
                @error('cover')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-semibold tracking-tight text-stone-900">Location &amp; visibility</h2>
                <button type="button" x-data
                        x-on:click="navigator.geolocation.getCurrentPosition(
                            position => $wire.useMyLocation(position.coords.latitude, position.coords.longitude),
                            () => alert('We could not access your location.')
                        )"
                        class="text-xs font-semibold text-emerald-800 hover:text-emerald-600">
                    Use my location
                </button>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label for="city" class="block text-sm font-medium text-stone-700">City</label>
                    <input id="city" type="text" wire:model="city"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label for="region" class="block text-sm font-medium text-stone-700">State / region</label>
                    <input id="region" type="text" wire:model="region"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-stone-700">Country</label>
                    <input id="country" type="text" wire:model="country" maxlength="2"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm uppercase shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    @error('country')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="latitude" class="block text-sm font-medium text-stone-700">Latitude</label>
                    <input id="latitude" type="number" step="0.0000001" wire:model="latitude"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    @error('latitude')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="longitude" class="block text-sm font-medium text-stone-700">Longitude</label>
                    <input id="longitude" type="number" step="0.0000001" wire:model="longitude"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    @error('longitude')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="visibility" class="block text-sm font-medium text-stone-700">Visibility</label>
                <select id="visibility" wire:model="visibility"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                    @foreach ($visibilities as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('visibility')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl bg-stone-100 p-4 text-sm text-stone-600">
                <p class="font-medium text-stone-800">A note on addresses</p>
                <p class="mt-1">
                    Share the city and region, not a street address, unless the layout is a public club
                    that welcomes visitors. Exact coordinates are optional.
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button type="button" wire:click="save" wire:loading.attr="disabled"
                class="rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-50">
            Save layout
        </button>
    </div>
</div>
