<div class="flex flex-wrap items-center gap-1.5">
    @foreach ($types as $type)
        @php
            $count = (int) ($counts[$type->value] ?? 0);
            $active = $mine === $type;
        @endphp
        <button type="button" wire:click="toggle('{{ $type->value }}')"
                title="{{ $type->label() }}"
                class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1.5 text-sm transition
                    {{ $active
                        ? 'border-emerald-300 bg-emerald-50 text-emerald-900'
                        : 'border-stone-200 bg-white text-stone-600 hover:border-emerald-200 hover:bg-stone-50' }}">
            <span class="text-base leading-none">{{ $type->emoji() }}</span>
            <span class="text-xs font-semibold">{{ $count }}</span>
        </button>
    @endforeach

    @guest
        <a href="{{ route('login') }}" class="ml-1 text-xs text-stone-500 hover:text-emerald-800">Sign in to react</a>
    @endguest
</div>
