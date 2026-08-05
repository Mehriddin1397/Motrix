<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold">Parolni unutdingizmi?</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Muammo emas. Email manzilingizni kiriting, sizga parolni tiklash havolasini yuboramiz.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1.5" type="email" name="email" :value="old('email')" required autofocus placeholder="siz@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full py-2.5">
            Tiklash havolasini yuborish
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('login') }}" class="font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400">← Kirish sahifasiga qaytish</a>
    </p>
</x-guest-layout>
