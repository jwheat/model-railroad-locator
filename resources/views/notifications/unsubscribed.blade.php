@php
    $labels = [
        'comments' => 'emails about comments on your content',
        'replies' => 'emails about replies to your comments',
        'follow_digest' => 'the weekly follow digest',
    ];
@endphp

<x-layouts.app title="Notification preferences">
    <div class="mx-auto max-w-xl rounded-3xl border border-stone-200 bg-white p-8 text-center shadow-sm">
        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-800">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-stone-900">You're unsubscribed</h1>
        <p class="mt-2 text-sm leading-6 text-stone-600">
            We won't send you {{ $labels[$type] ?? 'these emails' }} anymore. In-app notifications are still available.
        </p>
        <a href="{{ route('notifications.preferences') }}"
           class="mt-6 inline-block rounded-xl bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
            Review email preferences
        </a>
    </div>
</x-layouts.app>
