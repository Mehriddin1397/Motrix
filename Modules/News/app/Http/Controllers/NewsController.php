<?php

namespace Modules\News\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query()
            ->with(['category', 'author', 'media'])
            ->where('status', 'published');

        if ($cat = $request->input('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
        }

        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
            });
        }

        $news = $query->latest('published_at')->paginate(12)->withQueryString();

        $categories = NewsCategory::query()
            ->withCount(['news' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();

        return view('news::index', compact('news', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', News::class);

        return view('news::create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', News::class);
    }

    public function show(News $article)
    {
        $article->incrementQuietly('views_count');
        $article->load([
            'category',
            'author',
            'video',
            'comments.user',
        ]);

        $relatedArticles = News::query()
            ->with(['category', 'media'])
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->when($article->category_id, fn ($q) => $q->where('category_id', $article->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Calculate approximate reading time (average 180 words per min)
        $wordCount = str_word_count(strip_tags($article->body));
        $readTime = max(1, (int) ceil($wordCount / 180));

        return view('news::show', compact('article', 'relatedArticles', 'readTime'));
    }

    public function addComment(Request $request, News $article)
    {
        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $article->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->input('body'),
        ]);

        return redirect()->back()->with('status', 'Fikringiz muvaffaqiyatli qoldirildi!');
    }

    public function edit(News $article)
    {
        $this->authorize('update', $article);

        return view('news::edit', compact('article'));
    }

    public function update(Request $request, News $article)
    {
        $this->authorize('update', $article);
    }

    public function destroy(News $article)
    {
        $this->authorize('delete', $article);
    }
}
