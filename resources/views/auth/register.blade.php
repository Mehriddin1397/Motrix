<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold">Ro'yxatdan o'tish</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Motrix hamjamiyatiga qo'shiling</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Ism" />
            <x-text-input id="name" class="mt-1.5" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ismingiz" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1.5" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="siz@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Parol" />
            <x-text-input id="password" class="mt-1.5" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Parolni tasdiqlang" />
            <x-text-input id="password_confirmation" class="mt-1.5" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full py-2.5">
            Ro'yxatdan o'tish
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
        Hisobingiz bormi?
        <a href="{{ route('login') }}" class="font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400">Kirish</a>
    </p>
</x-guest-layout>
