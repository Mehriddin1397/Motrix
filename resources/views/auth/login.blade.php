<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold">Xush kelibsiz</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Hisobingizga kirish uchun ma'lumotlaringizni kiriting</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1.5" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="siz@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Parol" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400" href="{{ route('password.request') }}">
                        Parolni unutdingizmi?
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="mt-1.5" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex select-none items-center gap-2">
            <input id="remember_me" type="checkbox" class="rounded border-zinc-300 text-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800" name="remember">
            <span class="text-sm text-zinc-600 dark:text-zinc-400">Meni eslab qol</span>
        </label>

        <x-primary-button class="w-full py-2.5">
            Kirish
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
        Hisobingiz yo'qmi?
        <a href="{{ route('register') }}" class="font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400">Ro'yxatdan o'tish</a>
    </p>
</x-guest-layout>
