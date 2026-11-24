<x-layouts.app title="Reset password">
    <div class="mx-auto max-w-md">
        <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">Reset password</h1>
            <p class="mt-1 text-sm text-stone-500">Choose a new password for your account.</p>

            @if (session('status'))
                <p class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="password" class="block text-sm font-medium text-stone-700">New password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    @error('password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-stone-700">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    @error('password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-emerald-900 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-800">
                    Reset password
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-stone-500">
                <a href="{{ route('login') }}" class="font-semibold text-emerald-800 hover:underline">Back to sign in</a>
            </p>
        </div>
    </div>
</x-layouts.app>
