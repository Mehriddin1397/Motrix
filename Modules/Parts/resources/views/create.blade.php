<x-layout title="Yangi mahsulot">
    <div class="px-4 py-4">
        <div class="rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h1 class="mb-4 text-lg font-bold">Yangi mahsulot qo'shish</h1>

            <form method="POST" action="{{ route('parts.store') }}" enctype="multipart/form-data" class="space-y-4">
                @include('parts::_form')
            </form>
        </div>
    </div>
</x-layout>
