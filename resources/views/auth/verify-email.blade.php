<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold">Emailingizni tasdiqlang</h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Ro'yxatdan o'tganingiz uchun rahmat! Boshlashdan oldin, sizga yuborilgan havola orqali email manzilingizni tasdiqlang. Xat kelmagan bo'lsa, uni qayta yuborishimiz mumkin.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
            Ro'yxatdan o'tishda kiritilgan email manzilingizga yangi tasdiqlash havolasi yuborildi.
        </div>
    @endif

    <div class="flex flex-col items-center gap-3">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full">
            @csrf
            <x-primary-button class="w-full py-2.5">
                Tasdiqlash xatini qayta yuborish
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-zinc-500 underline hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                Chiqish
            </button>
        </form>
    </div>
</x-guest-layout>
