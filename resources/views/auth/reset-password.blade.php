<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold">Yangi parol o'rnatish</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Hisobingiz uchun yangi parol yarating</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1.5" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Yangi parol" />
            <x-text-input id="password" class="mt-1.5" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Parolni tasdiqlang" />
            <x-text-input id="password_confirmation" class="mt-1.5" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full py-2.5">
            Parolni tiklash
        </x-primary-button>
    </form>
</x-guest-layout>
