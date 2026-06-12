<x-guest-layout>
    <div class="relative rounded-3xl border border-slate-700/50 bg-slate-950/95 p-8 shadow-2xl shadow-slate-950/40 backdrop-blur-xl sm:p-10">
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-sky-500 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-white shadow-lg shadow-indigo-500/20">
                Password recovery
            </div>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-slate-100">Forgot your password?</h2>
            <p class="mt-3 text-sm text-slate-300 sm:text-base">Enter your email and we will send a secure reset link to your inbox.</p>
        </div>

        <x-auth-session-status class="mb-6 rounded-2xl bg-green-50 p-4 text-sm text-green-700" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-slate-700" />
                <x-text-input id="email" class="mt-2 block w-full rounded-3xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
            </div>

            <div class="rounded-3xl border border-slate-700/50 bg-slate-900/80 p-4 text-sm text-slate-200">
                We will send a secure reset link to the email address associated with your account.
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-3xl border border-slate-700/50 bg-slate-800 px-5 py-3 text-sm font-semibold text-slate-100 shadow-sm transition hover:border-indigo-400 hover:text-white">
                    Back to login
                </a>
                <x-primary-button class="w-full sm:w-auto rounded-3xl px-8 py-3 text-sm font-semibold">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
