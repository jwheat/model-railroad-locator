<x-layouts.app title="Forgot your password">
    <div class="mx-auto max-w-md">
        <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">Reset your password</h1>
            <p class="mt-1 text-sm text-stone-500">Tell us the email you registered with and we'll send you a reset link.</p>

            @if (session('status'))
                <p class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-stone-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    @error('email')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-emerald-900 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-800">
                    Send reset link
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-stone-500">
                Remembered it?
                <a href="{{ route('login') }}" class="font-semibold text-emerald-800 hover:underline">Back to sign in</a>
            </p>
        </div>
    </div>
</x-layouts.app>
