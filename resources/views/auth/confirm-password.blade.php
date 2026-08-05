<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold">Parolni tasdiqlang</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Bu ilovaning himoyalangan qismi. Davom etishdan oldin parolingizni tasdiqlang.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="Parol" />
            <x-text-input id="password" class="mt-1.5" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full py-2.5">
            Tasdiqlash
        </x-primary-button>
    </form>
</x-guest-layout>
