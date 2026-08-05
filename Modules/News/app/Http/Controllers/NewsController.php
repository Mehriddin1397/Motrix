<?php

namespace Modules\News\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\News\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::query()
            ->with(['category', 'author'])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(10);

        return view('news::index', compact('news'));
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
        $article->load(['category', 'author']);

        return view('news::show', compact('article'));
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
