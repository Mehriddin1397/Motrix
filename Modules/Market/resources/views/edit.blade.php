<x-layout title="E'lonni tahrirlash">
    <div class="px-4 py-4">
        <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h1 class="mb-4 text-lg font-bold">E'lonni tahrirlash</h1>

            <form method="POST" action="{{ route('market.update', $listing) }}" enctype="multipart/form-data" class="space-y-4">
                @include('market::_form')
            </form>

            <form method="POST" action="{{ route('market.destroy', $listing) }}" class="mt-3" onsubmit="return confirm('E\'lonni o\'chirishga ishonchingiz komilmi?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-full border border-red-200 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 dark:border-red-900/40 dark:text-red-400 dark:hover:bg-red-950/30">
                    E'lonni o'chirish
                </button>
            </form>
        </div>
    </div>
</x-layout>
