<div>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-stone-900">Edit profile</h1>
            <p class="mt-1 text-sm text-stone-500">How members see you across the directory and the blog.</p>
        </div>
        <a href="{{ route('members.show', auth()->user()) }}"
           class="text-sm font-semibold text-emerald-800 hover:text-emerald-600">
            &larr; Back to my profile
        </a>
    </div>

    <form wire:submit="save" class="mt-8 max-w-2xl space-y-6 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex items-center gap-5">
            <span class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-emerald-900 text-2xl font-bold text-amber-300">
                @if ($user->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->displayName() }}" class="h-full w-full object-cover">
                @else
                    {{ $user->initials() }}
                @endif
            </span>
            <div>
                <label class="block text-sm font-semibold text-stone-700">Avatar</label>
                <input type="file" wire:model="avatar" accept="image/*"
                       class="mt-1 block w-full text-sm text-stone-600 file:mr-4 file:rounded-xl file:border-0 file:bg-stone-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-stone-700 hover:file:bg-stone-200">
                <p class="mt-1 text-xs text-stone-400">Square images work best. Stored on the public disk.</p>
            </div>
        </div>

        <div>
            <label for="displayName" class="block text-sm font-semibold text-stone-700">Display name</label>
            <input id="displayName" type="text" wire:model="displayName" placeholder="How should we call you?"
                   class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
            <p class="mt-1 text-xs text-stone-400">Shown on stories, layouts, and comments. Leave blank to keep your name.</p>
            @error('displayName')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="bio" class="block text-sm font-semibold text-stone-700">Bio</label>
            <textarea id="bio" wire:model="bio" rows="4" placeholder="Tell the club what you model, what you've built, and what's next."
                      class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"></textarea>
            @error('bio')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="city" class="block text-sm font-semibold text-stone-700">City</label>
                <input id="city" type="text" wire:model="city" class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
            </div>
            <div>
                <label for="region" class="block text-sm font-semibold text-stone-700">Region</label>
                <input id="region" type="text" wire:model="region" class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
            </div>
            <div>
                <label for="country" class="block text-sm font-semibold text-stone-700">Country code</label>
                <input id="country" type="text" wire:model="country" maxlength="2" class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" wire:loading.attr="disabled"
                    class="rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-50">
                Save profile
            </button>
            @if (session('status'))
                <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
            @endif
        </div>
    </form>
</div>
