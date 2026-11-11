@props(['user', 'size' => 'md'])

@php
    $dimensions = match ($size) {
        'sm' => 'h-8 w-8 text-xs',
        'lg' => 'h-20 w-20 text-2xl',
        default => 'h-10 w-10 text-sm',
    };
@endphp

@if ($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->displayName() }}"
         {{ $attributes->merge(['class' => "{$dimensions} rounded-full object-cover ring-2 ring-white/20"]) }}>
@else
    <span {{ $attributes->merge(['class' => "{$dimensions} inline-flex items-center justify-center rounded-full bg-emerald-800 font-semibold text-amber-200 ring-2 ring-white/10"]) }}
          title="{{ $user->displayName() }}">
        {{ $user->initials() }}
    </span>
@endif
