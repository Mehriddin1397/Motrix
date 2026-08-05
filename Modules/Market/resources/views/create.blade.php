<x-layout title="Yangi e'lon">
    <div class="px-4 py-4">
        <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h1 class="mb-4 text-lg font-bold">Yangi e'lon joylashtirish</h1>

            <form method="POST" action="{{ route('market.store') }}" enctype="multipart/form-data" class="space-y-4">
                @include('market::_form')
            </form>
        </div>
    </div>
</x-layout>
