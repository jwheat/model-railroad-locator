<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · Model Railroad Locator' : 'Model Railroad Locator' }}</title>
    <meta name="description" content="{{ $description ?? 'Discover model railroad layouts, follow club build reports, and share your own stories.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-stone-50 text-stone-900 antialiased">
    <header class="border-b border-emerald-900/20 bg-emerald-950 text-stone-100">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-8 gap-y-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-emerald-950 shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                        <path d="M6 3v18M18 3v18M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-sm font-semibold tracking-[0.18em] text-amber-400 uppercase">Model Railroad</span>
                    <span class="block text-lg font-semibold tracking-tight">Locator</span>
                </span>
            </a>

            <nav class="order-3 flex w-full items-center gap-1 text-sm font-medium sm:order-none sm:w-auto">
                <a href="{{ route('layouts.index') }}"
                   class="rounded-lg px-3 py-2 transition hover:bg-white/10 {{ request()->routeIs('layouts.*') ? 'bg-white/10 text-white' : 'text-stone-300' }}">
                    Layouts
                </a>
                <a href="{{ route('posts.index') }}"
                   class="rounded-lg px-3 py-2 transition hover:bg-white/10 {{ request()->routeIs('posts.*') ? 'bg-white/10 text-white' : 'text-stone-300' }}">
                    Stories
                </a>
                <a href="{{ route('layouts.index') }}#near-me"
                   class="rounded-lg px-3 py-2 text-stone-300 transition hover:bg-white/10">
                    Near me
                </a>
            </nav>

            <div class="ml-auto flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('posts.create') }}"
                       class="rounded-lg bg-amber-500 px-3 py-2 font-semibold text-emerald-950 transition hover:bg-amber-400">
                        Write a story
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="/admin" class="hidden text-stone-300 hover:text-white sm:inline">Admin</a>
                    @endif
                    <a href="{{ route('members.show', auth()->user()) }}" class="flex items-center gap-2 text-stone-200 hover:text-white">
                        <x-avatar :user="auth()->user()" size="sm" />
                        <span class="hidden sm:inline">{{ auth()->user()->displayName() }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-stone-400 transition hover:text-white">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-stone-300 transition hover:text-white">Sign in</a>
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-amber-500 px-3 py-2 font-semibold text-emerald-950 transition hover:bg-amber-400">
                        Join free
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="border-b border-emerald-200 bg-emerald-50">
            <p class="mx-auto max-w-7xl px-4 py-3 text-sm font-medium text-emerald-800 sm:px-6 lg:px-8">
                {{ session('status') }}
            </p>
        </div>
    @endif

    <main class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-stone-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} Model Railroad Locator. Built by hobbyists, for hobbyists.</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('layouts.index') }}" class="hover:text-stone-900">Browse layouts</a>
                <a href="{{ route('posts.index') }}" class="hover:text-stone-900">Read stories</a>
                <a href="{{ route('register') }}" class="hover:text-stone-900">Create a free account</a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
