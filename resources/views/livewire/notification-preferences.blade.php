<div class="mx-auto max-w-2xl">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-stone-900">Email preferences</h1>
            <p class="mt-1 text-sm text-stone-500">In-app notifications stay on. These control which emails we send.</p>
        </div>
        <a href="{{ route('notifications.index') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-600">
            &larr; Back to notifications
        </a>
    </div>

    <form wire:submit="save" class="mt-8 space-y-4 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-stone-200 p-4 hover:bg-stone-50">
            <input type="checkbox" wire:model="comments" class="mt-1 h-4 w-4 rounded border-stone-300 text-emerald-800 focus:ring-emerald-500">
            <span>
                <span class="block text-sm font-semibold text-stone-900">Comments on my stories and layouts</span>
                <span class="mt-0.5 block text-sm text-stone-600">Email me when someone leaves a comment on a story or layout I own.</span>
            </span>
        </label>

        <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-stone-200 p-4 hover:bg-stone-50">
            <input type="checkbox" wire:model="replies" class="mt-1 h-4 w-4 rounded border-stone-300 text-emerald-800 focus:ring-emerald-500">
            <span>
                <span class="block text-sm font-semibold text-stone-900">Replies to my comments</span>
                <span class="mt-0.5 block text-sm text-stone-600">Email me when someone replies to a comment I wrote.</span>
            </span>
        </label>

        <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-stone-200 p-4 hover:bg-stone-50">
            <input type="checkbox" wire:model="digest" class="mt-1 h-4 w-4 rounded border-stone-300 text-emerald-800 focus:ring-emerald-500">
            <span>
                <span class="block text-sm font-semibold text-stone-900">Weekly follow digest</span>
                <span class="mt-0.5 block text-sm text-stone-600">One weekly email with new stories from the layouts, clubs, and members I follow.</span>
            </span>
        </label>

        <div class="flex items-center justify-between gap-4 pt-2">
            <p class="text-xs text-stone-500">Every email includes a one-click unsubscribe link.</p>
            <button type="submit" wire:loading.attr="disabled"
                    class="rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-50">
                Save preferences
            </button>
        </div>
    </form>
</div>
