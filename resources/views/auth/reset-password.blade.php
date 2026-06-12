<x-guest-layout>
    <div class="relative rounded-3xl border border-slate-700/50 bg-slate-950/95 p-8 shadow-2xl shadow-slate-950/40 backdrop-blur-xl sm:p-10">
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-sky-500 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-white shadow-lg shadow-indigo-500/20">
                Reset password
            </div>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-slate-100">Choose a new password</h2>
            <p class="mt-3 text-sm text-slate-300 sm:text-base">Enter your email and new password to recover access to your account.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-slate-700" />
                <x-text-input id="email" class="mt-2 block w-full rounded-3xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
            </div>

            <div>
                <x-input-label for="password" :value="__('New Password')" class="text-sm font-semibold text-slate-700" />
                <x-text-input id="password" class="mt-2 block w-full rounded-3xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-semibold text-slate-700" />
                <x-text-input id="password_confirmation" class="mt-2 block w-full rounded-3xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
            </div>

            <div class="rounded-3xl border border-slate-700/50 bg-slate-900/80 p-4 text-sm text-slate-200">
                Use a strong password with letters, numbers, and symbols for best security.
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-3xl border border-slate-700/50 bg-slate-800 px-5 py-3 text-sm font-semibold text-slate-100 shadow-sm transition hover:border-indigo-400 hover:text-white">
                    Back to login
                </a>
                <x-primary-button class="w-full sm:w-auto rounded-3xl px-8 py-3 text-sm font-semibold">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
