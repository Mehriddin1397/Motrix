<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\News\Models\News;
use Modules\Video\Models\Video;

class HomeController extends Controller
{
    /**
     * Total number of preloaded scrollytelling frames (public/frames/ezgif-frame-001.jpg ... 080.jpg).
     */
    private const FRAME_COUNT = 80;

    public function __invoke()
    {
        $frames = collect(range(1, self::FRAME_COUNT))
            ->map(fn (int $i) => asset(sprintf('frames/ezgif-frame-%03d.jpg', $i)))
            ->all();

        $categories = MotorcycleCategory::query()
            ->withCount(['motorcycles' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();

        $popularBrands = Brand::query()
            ->has('motorcycles')
            ->withCount(['motorcycles' => fn ($q) => $q->where('status', 'published')])
            ->orderByDesc('motorcycles_count')
            ->limit(8)
            ->get();

        $popular = Motorcycle::query()
            ->with(['brand', 'specification'])
            ->where('status', 'published')
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        $newest = Motorcycle::query()
            ->with(['brand', 'specification'])
            ->where('status', 'published')
            ->latest('id')
            ->limit(8)
            ->get();

        $activeQuiz = Quiz::query()
            ->withCount('questions')
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $featuredVideos = Video::query()
            ->with(['category', 'motorcycle.brand'])
            ->latest('published_at')
            ->limit(6)
            ->get();

        $knowledgeArticles = News::query()
            ->with(['category', 'media'])
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(4)
            ->get();

        $listings = Listing::query()
            ->with(['motorcycle.brand', 'city', 'media'])
            ->where('status', 'active')
            ->latest('published_at')
            ->limit(8)
            ->get();

        return view('home', compact(
            'frames',
            'categories',
            'popularBrands',
            'popular',
            'newest',
            'activeQuiz',
            'featuredVideos',
            'knowledgeArticles',
            'listings'
        ));
    }
}
