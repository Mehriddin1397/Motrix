<x-layout 
    title="Videolar — Motrix Media"
    description="Motrix rasmiy videolari, ko'rsatuvlar va ijtimoiy tarmoqlardagi so'nggi videoroliklar."
>
    <div class="max-w-5xl mx-auto px-4 py-5">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">
                    Motrix Videolar
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    So'nggi sharhlar, ko'rsatuvlar va rasmiy videoroliklar
                </p>
            </div>
        </div>

        @if($posts->isEmpty())
            <div class="rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-800 p-12 text-center">
                <div class="text-4xl mb-2">🎬</div>
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Hozircha videolar mavjud emas</h3>
                <p class="text-xs text-zinc-500 mt-1">Tez kunda yangi videoroliklar yuklanadi.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                @foreach($posts as $post)
                    <a href="{{ route('video-posts.show', $post->slug) }}" class="group block rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm hover:border-amber-500/50 hover:shadow-md transition">
                        <div class="aspect-video bg-black relative overflow-hidden">
                            @if($post->thumbnail_url)
                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-600 bg-zinc-900">
                                    <span class="text-3xl">▶</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 line-clamp-2 group-hover:text-amber-500 transition">
                                {{ $post->title }}
                            </h2>
                            @if($post->description)
                                <p class="text-xs text-zinc-500 line-clamp-2 mt-1">
                                    {{ Str::limit(strip_tags($post->description), 90) }}
                                </p>
                            @endif
                            <div class="mt-3 flex items-center justify-between text-[11px] text-zinc-400">
                                <span>{{ $post->created_at->format('d.m.Y') }}</span>
                                <span>{{ number_format($post->views_count) }} ko'rildi</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</x-layout>
