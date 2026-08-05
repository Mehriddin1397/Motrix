<?php

namespace App\Http\Controllers;

use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\News\Models\News;
use Modules\Video\Models\Video;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = MotorcycleCategory::query()->orderBy('name')->get();

        $popular = Motorcycle::query()
            ->with(['brand', 'specification'])
            ->where('status', 'published')
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        $newest = Motorcycle::query()
            ->with(['brand', 'specification'])
            ->where('status', 'published')
            ->latest()
            ->limit(8)
            ->get();

        $aiVideos = Video::query()
            ->with('motorcycle.brand')
            ->where('is_ai_generated', true)
            ->latest('published_at')
            ->limit(6)
            ->get();

        $listings = Listing::query()
            ->with(['motorcycle.brand', 'city'])
            ->where('status', 'active')
            ->latest('published_at')
            ->limit(6)
            ->get();

        $news = News::query()
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('home', compact('categories', 'popular', 'newest', 'aiVideos', 'listings', 'news'));
    }
}
