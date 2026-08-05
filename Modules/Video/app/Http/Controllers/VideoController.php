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
        $videos = Video::query()
            ->with(['category', 'motorcycle.brand'])
            ->when($request->category, fn ($query, $slug) => $query->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = VideoCategory::query()->orderBy('name')->get();

        return view('video::index', compact('videos', 'categories'));
    }

    public function create()
    {
        return view('video::create');
    }

    public function store(Request $request) {}

    public function show(Video $video)
    {
        $video->load(['category', 'motorcycle.brand']);
        $video->increment('views_count');

        return view('video::show', compact('video'));
    }

    public function edit(Video $video)
    {
        return view('video::edit', compact('video'));
    }

    public function update(Request $request, Video $video) {}

    public function destroy(Video $video) {}
}
