<section>
    <header>
        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">
            Parolni yangilash
        </h2>

        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            Hisobingiz xavfsizligini ta'minlash uchun uzun va tasodifiy paroldan foydalaning.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Joriy parol" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Yangi parol" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Yangi parolni tasdiqlang" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button class="bg-amber-500 text-zinc-950 font-bold hover:bg-amber-400 shadow-sm">
                Parolni yangilash
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-semibold text-emerald-600 dark:text-emerald-400"
                >✓ Parol muvaffaqiyatli yangilandi.</p>
            @endif
        </div>
    </form>
</section>
