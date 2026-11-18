<section class="space-y-6">
    <header>
        <h2 class="text-base font-bold text-red-600 dark:text-red-400">
            Hisobni o'chirish
        </h2>

        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            Hisobingiz o'chirilgach, unga tegishli barcha e'lonlar, xabarlar va ma'lumotlar butunlay o'chib ketadi.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-500 text-white font-semibold"
    >Hisobni butunlay o'chirish</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-zinc-900 rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                Hisobingizni o'chirishga ishonchingiz komilmi?
            </h2>

            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                Hisobingiz o'chirilgach, unga tegishli barcha resurs va ma'lumotlar butunlay o'chib ketadi. Hisobni butunlay o'chirishni tasdiqlash uchun parolingizni kiriting.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Parol" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full text-zinc-900 dark:text-zinc-100"
                    placeholder="Parolingizni kiriting"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="text-zinc-700 dark:text-zinc-300">
                    Bekor qilish
                </x-secondary-button>

                <x-danger-button class="bg-red-600 hover:bg-red-500 text-white font-semibold">
                    Hisobni o'chirish
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
