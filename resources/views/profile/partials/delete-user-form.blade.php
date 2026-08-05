<section class="space-y-6">
    <header>
        <h2 class="text-sm font-bold">
            Hisobni o'chirish
        </h2>

        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            Hisobingiz o'chirilgach, unga tegishli barcha resurs va ma'lumotlar butunlay o'chib ketadi. Hisobni o'chirishdan oldin saqlab qolmoqchi bo'lgan ma'lumotlaringizni yuklab oling.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hisobni o'chirish</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                Hisobingizni o'chirishga ishonchingiz komilmi?
            </h2>

            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Hisobingiz o'chirilgach, unga tegishli barcha resurs va ma'lumotlar butunlay o'chib ketadi. Hisobni butunlay o'chirishni tasdiqlash uchun parolingizni kiriting.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Parol" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Parol"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Bekor qilish
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Hisobni o'chirish
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
