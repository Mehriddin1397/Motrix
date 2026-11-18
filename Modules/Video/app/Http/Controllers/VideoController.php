<?php

namespace Modules\Video\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Video\Models\Video;
use Modules\Video\Models\VideoCategory;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::query()
            ->with(['category', 'motorcycle.brand']);

        if ($cat = $request->input('category')) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $cat));
        }

        if ($q = trim($request->input('q', ''))) {
            $query->where('title', 'like', "%{$q}%");
        }

        if ($moto = $request->input('motorcycle')) {
            $query->whereHas('motorcycle', fn ($m) => $m->where('slug', $moto));
        }

        $videos = $query->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = VideoCategory::query()
            ->withCount('videos')
            ->orderBy('name')
            ->get();

        return view('video::index', compact('videos', 'categories'));
    }

    public function create()
    {
        return view('video::create');
    }

    public function store(Request $request) {}

    public function show(Video $video)
    {
        $video->load(['category', 'motorcycle.brand', 'motorcycle.specification', 'articles']);
        $video->increment('views_count');

        $relatedVideos = Video::query()
            ->with(['category', 'motorcycle.brand'])
            ->where('id', '!=', $video->id)
            ->when($video->category_id, fn ($q) => $q->where('category_id', $video->category_id))
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('video::show', compact('video', 'relatedVideos'));
    }

    public function edit(Video $video)
    {
        return view('video::edit', compact('video'));
    }

    public function update(Request $request, Video $video) {}

    public function destroy(Video $video) {}
}
