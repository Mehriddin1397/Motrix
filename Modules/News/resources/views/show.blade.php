@php
    $newsDesc = \Illuminate\Support\Str::limit(strip_tags($article->body), 160);
    $coverUrl = $article->getFirstMediaUrl('cover') ?: null;
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $article->title,
        'description' => $newsDesc,
        'image' => $coverUrl ? [$coverUrl] : [],
        'datePublished' => $article->published_at?->toIso8601String(),
        'dateModified' => $article->updated_at?->toIso8601String(),
        'author' => [
            '@type' => 'Person',
            'name' => $article->author->name ?? 'Motrix.uz',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Motrix',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => 'https://motrix.uz/images/logo-icon-black.png',
            ],
        ],
    ];
@endphp

<x-layout 
    :title="$article->title . ' — Motrix Bilimlar bazasi'"
    :description="$newsDesc"
    :image="$coverUrl"
    type="article"
    :schema="$articleSchema"
>
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-1.5 px-4 pt-3 pb-2 text-xs text-zinc-500 dark:text-zinc-400 overflow-x-auto scrollbar-none">
        <a href="{{ route('home') }}" class="hover:text-amber-500 shrink-0">Bosh sahifa</a>
        <span class="shrink-0">/</span>
        <a href="{{ route('news.index') }}" class="hover:text-amber-500 shrink-0">Bilimlar bazasi</a>
        @if($article->category)
            <span class="shrink-0">/</span>
            <a href="{{ route('news.index', ['category' => $article->category->slug]) }}" class="hover:text-amber-500 shrink-0">{{ $article->category->name }}</a>
        @endif
        <span class="shrink-0">/</span>
        <span class="text-zinc-800 dark:text-zinc-200 font-medium truncate">{{ $article->title }}</span>
    </nav>

    {{-- Cover Media --}}
    <div class="relative aspect-[16/9] w-full overflow-hidden bg-gradient-to-br from-amber-100 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900 sm:aspect-[21/9]">
        @if($article->getFirstMediaUrl('cover'))
            <img src="{{ $article->getFirstMediaUrl('cover') }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center text-6xl">📚</div>
        @endif

        @if($article->category)
            <div class="absolute left-3 top-3">
                <a
                    href="{{ route('news.index', ['category' => $article->category->slug]) }}"
                    class="rounded-full bg-black/60 px-3 py-1 text-xs font-bold text-amber-400 backdrop-blur shadow-sm hover:bg-black/80"
                >
                    {{ $article->category->name }}
                </a>
            </div>
        @endif

        <div class="absolute right-3 bottom-3 flex items-center gap-2">
            <span class="rounded-full bg-black/50 px-2.5 py-1 text-xs font-medium text-white/90 backdrop-blur-sm">
                ⏱️ {{ $readTime }} daqiqa mutolaa
            </span>
        </div>
    </div>

    {{-- Main Article Container --}}
    <div class="mx-auto max-w-3xl space-y-6 px-4 py-6">

        {{-- Title and Meta --}}
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-4xl">
                {{ $article->title }}
            </h1>

            <div class="mt-3 flex flex-wrap items-center gap-2.5 border-b border-zinc-100 pb-4 text-xs text-zinc-500 dark:border-zinc-800/80 dark:text-zinc-400">
                <div class="flex items-center gap-1.5 font-medium text-zinc-700 dark:text-zinc-300">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400">
                        {{ mb_substr($article->author->name ?? 'M', 0, 1) }}
                    </span>
                    <span>{{ $article->author->name ?? 'Motrix tahririyati' }}</span>
                </div>
                <span>•</span>
                <span>{{ $article->published_at?->format('d.m.Y H:i') }}</span>
                <span>•</span>
                <span class="inline-flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ number_format($article->views_count ?? 1, 0, '', ' ') }} marta o'qildi</span>
                </span>
            </div>
        </div>

        {{-- Article Body (Readable Typography) --}}
        <article class="prose prose-zinc dark:prose-invert max-w-none text-base leading-relaxed space-y-4">
            @if(Str::contains($article->body, ['<p>', '<div>', '<h3>', '<h2>', '<img', '<table>']))
                {!! $article->body !!}
            @else
                {!! nl2br(e($article->body)) !!}
            @endif
        </article>

        {{-- Connected Video Section --}}
        @if($article->video)
            <div class="rounded-3xl border border-amber-500/20 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent p-5 dark:border-amber-500/30">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📹</span>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                Mavzuga oid video qo'llanma
                            </span>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">
                                {{ $article->video->title }}
                            </h3>
                        </div>
                    </div>
                    <a
                        href="{{ route('video.show', $article->video->slug) }}"
                        class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-amber-500 dark:text-amber-400 shrink-0"
                    >
                        Video markazida ochish →
                    </a>
                </div>

                <div class="relative aspect-video w-full overflow-hidden rounded-2xl bg-black shadow-lg">
                    @if($article->video->is_direct_video)
                        <video src="{{ $article->video->embed_url }}" controls class="h-full w-full"></video>
                    @elseif($article->video->embed_url)
                        <iframe
                            src="{{ $article->video->embed_url }}"
                            title="{{ $article->video->title }}"
                            class="h-full w-full border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    @endif
                </div>
            </div>
        @endif

        {{-- Interactive Feedback / Voting Widget (TT 2.C) --}}
        <div
            x-data="{
                voted: localStorage.getItem('motrix_article_vote_{{ $article->id }}') || null,
                vote(type) {
                    this.voted = type;
                    localStorage.setItem('motrix_article_vote_{{ $article->id }}', type);
                }
            }"
            class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 text-center dark:border-zinc-800 dark:bg-zinc-900/60"
        >
            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                Ushbu maqola sizga foydali bo'ldimi?
            </div>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                Ovozingiz bizga sifatliroq va foydaliroq kontent tayyorlashga yordam beradi
            </p>

            <div class="mt-3 flex items-center justify-center gap-3">
                <button
                    type="button"
                    @click="vote('yes')"
                    :disabled="voted !== null"
                    class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
                    :class="voted === 'yes' ? 'bg-emerald-600 text-white' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-emerald-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200'"
                >
                    <span>👍 Ha, foydali</span>
                </button>
                <button
                    type="button"
                    @click="vote('no')"
                    :disabled="voted !== null"
                    class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold transition"
                    :class="voted === 'no' ? 'bg-rose-600 text-white' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-rose-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200'"
                >
                    <span>👎 Yetarli emas</span>
                </button>
            </div>

            <div x-show="voted" x-transition class="mt-2 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                Fikr bildirganingiz uchun rahmat!
            </div>
        </div>

        {{-- Helpful Links & Actions Banner --}}
        <div class="rounded-2xl border border-amber-500/20 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent p-4 dark:border-amber-500/30">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide text-amber-600 dark:text-amber-400">💡 Qiziqish bildirgan mototsiklingiz bormi?</div>
                    <div class="mt-1 text-sm font-bold text-zinc-900 dark:text-zinc-100">Katalogda texnik parametrlarini solishtiring</div>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">O'zbekistondagi barcha modellarning to'liq xususiyatlari, narxlari va sharhlari.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('motorcycle.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                    >
                        Katalogga o'tish →
                    </a>
                    <a
                        href="{{ route('comparison.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full border border-zinc-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
                    >
                        ⚖️ Taqqoslash
                    </a>
                </div>
            </div>
        </div>

        {{-- Related Articles Section --}}
        @if($relatedArticles->isNotEmpty())
            <div class="border-t border-zinc-100 pt-6 dark:border-zinc-800">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-base font-bold">Mavzuga oid o'xshash maqolalar</h2>
                    <a href="{{ route('news.index', ['category' => $article->category?->slug]) }}" class="text-xs font-medium text-amber-500 hover:underline">
                        Kategoriya bo'yicha barchasi →
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach($relatedArticles as $related)
                        <a
                            href="{{ route('news.show', $related) }}"
                            class="flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white p-3 shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
                        >
                            <div class="aspect-video w-full overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                @if($related->getFirstMediaUrl('cover'))
                                    <img src="{{ $related->getFirstMediaUrl('cover') }}" alt="{{ $related->title }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-xl">📰</div>
                                @endif
                            </div>
                            <h3 class="mt-2 text-xs font-bold leading-snug line-clamp-2 text-zinc-900 dark:text-zinc-100">
                                {{ $related->title }}
                            </h3>
                            <div class="mt-2 text-[10px] text-zinc-400">
                                {{ $related->published_at?->format('d.m.Y') }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Comments / Discussions Section (TT 2.C) --}}
        <div class="border-t border-zinc-100 pt-6 dark:border-zinc-800" id="comments">
            <h2 class="text-base font-bold">
                Fikr-mulohazalar va muhokama
                <span class="text-xs font-normal text-zinc-400">({{ $article->comments->count() }})</span>
            </h2>

            @if(session('status'))
                <div class="mt-3 rounded-xl bg-emerald-50 p-3 text-xs font-medium text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    ✅ {{ session('status') }}
                </div>
            @endif

            {{-- New Comment Form --}}
            <div class="mt-4 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @auth
                    <form method="POST" action="{{ route('news.comments.store', $article) }}">
                        @csrf
                        <div class="mb-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            Fikringizni qoldiring ({{ auth()->user()->name }} sifatida):
                        </div>
                        <textarea
                            name="body"
                            rows="3"
                            required
                            placeholder="Ushbu mavzu yoki tajribangiz bo'yicha fikringizni yozing..."
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-900 placeholder:text-zinc-400 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-zinc-800 dark:bg-zinc-800/60 dark:text-zinc-100"
                        ></textarea>
                        <div class="mt-2 flex justify-end">
                            <button
                                type="submit"
                                class="rounded-full bg-amber-500 px-4 py-1.5 text-xs font-bold text-white shadow transition hover:bg-amber-400"
                            >
                                Fikr qoldirish
                            </button>
                        </div>
                    </form>
                @else
                    <div class="flex items-center justify-between gap-3 text-xs">
                        <span class="text-zinc-500 dark:text-zinc-400">Muhokamada qatnashish va fikr qoldirish uchun tizimga kiring:</span>
                        <a
                            href="{{ route('login') }}"
                            class="shrink-0 rounded-full bg-amber-500 px-4 py-1.5 text-xs font-bold text-white shadow hover:bg-amber-400"
                        >
                            Kirish / Ro'yxatdan o'tish
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Comments List --}}
            <div class="mt-4 space-y-3">
                @forelse($article->comments as $comment)
                    <div class="rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-900/60">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400">
                                    {{ mb_substr($comment->user->name ?? 'F', 0, 1) }}
                                </span>
                                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $comment->user->name ?? 'Foydalanuvchi' }}
                                </span>
                            </div>
                            <span class="text-[10px] text-zinc-400">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300">
                            {{ $comment->body }}
                        </p>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-zinc-400">
                        Hozircha birorta ham fikr qoldirilmagan. Birinchi bo'lib o'z fikringizni bildiring!
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-layout>
