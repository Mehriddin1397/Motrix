<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\Parts\Models\Part;
use Modules\ServiceCenter\Models\ServiceProvider;
use Modules\Video\Models\Video;

/**
 * Renders /sitemap.xml from the actual named routes and published/active
 * database rows, so it can never point at a draft, unpublished, or
 * otherwise non-canonical URL. Result is cached because it is rebuilt from
 * several full-table queries; new/updated rows appear within the TTL below
 * without any manual regeneration step.
 */
class SitemapController extends Controller
{
    private const CACHE_TTL_SECONDS = 3600;

    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', self::CACHE_TTL_SECONDS, fn () => $this->build());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function build(): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $dom->appendChild($urlset);

        foreach ($this->urls() as [$loc, $lastmod, $priority, $changefreq]) {
            $this->appendUrl($dom, $urlset, $this->canonicalUrl($loc), $lastmod, $priority, $changefreq);
        }

        return $dom->saveXML();
    }

    /**
     * @return list<array{0: string, 1: ?Carbon, 2: string, 3: string}>
     */
    private function urls(): array
    {
        $urls = [];

        // Static / high-level index pages
        $staticPages = [
            'home' => ['1.0', 'daily'],
            'market.index' => ['0.9', 'daily'],
            'motorcycle.index' => ['0.9', 'daily'],
            'quiz.index' => ['0.8', 'daily'],
            'news.index' => ['0.8', 'daily'],
            'parts.index' => ['0.8', 'daily'],
            'servicecenter.index' => ['0.8', 'daily'],
            'video.index' => ['0.7', 'weekly'],
            'comparison.index' => ['0.7', 'weekly'],
        ];

        foreach ($staticPages as $route => [$priority, $changefreq]) {
            $urls[] = [route($route), null, $priority, $changefreq];
        }

        // Active quizzes and leaderboards
        foreach (Quiz::query()->where('is_active', true)->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $quiz) {
            $urls[] = [route('quiz.show', $quiz), $quiz->updated_at, '0.8', 'weekly'];
            $urls[] = [route('quiz.leaderboard', $quiz), $quiz->updated_at, '0.6', 'weekly'];
        }

        // Motorcycle Brands
        foreach (Brand::query()->has('motorcycles')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $brand) {
            $urls[] = [route('motorcycle.index', ['brand' => $brand->slug]), $brand->updated_at, '0.8', 'weekly'];
        }

        // Motorcycle Categories
        foreach (MotorcycleCategory::query()->has('motorcycles')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $category) {
            $urls[] = [route('motorcycle.index', ['category' => $category->slug]), $category->updated_at, '0.8', 'weekly'];
        }

        // Knowledge Base Categories
        foreach (NewsCategory::query()->has('news')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $newsCategory) {
            $urls[] = [route('news.index', ['category' => $newsCategory->slug]), $newsCategory->updated_at, '0.7', 'weekly'];
        }

        // Active listings on the market
        foreach (Listing::query()->where('status', 'active')->select(['id', 'updated_at'])->orderBy('id')->get() as $listing) {
            $urls[] = [route('market.show', $listing), $listing->updated_at, '0.8', 'daily'];
        }

        // Published motorcycle catalog items
        foreach (Motorcycle::query()->where('status', 'published')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $motorcycle) {
            $urls[] = [route('motorcycle.show', $motorcycle), $motorcycle->updated_at, '0.8', 'weekly'];
        }

        // Sellers with active listings
        foreach (User::query()->whereHas('listings', fn($q) => $q->where('status', 'active'))->select(['id', 'updated_at'])->orderBy('id')->get() as $seller) {
            $urls[] = [route('sellers.show', $seller), $seller->updated_at, '0.7', 'weekly'];
        }

        // Spare parts
        foreach (Part::query()->where('status', 'active')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $part) {
            $urls[] = [route('parts.show', $part), $part->updated_at, '0.7', 'weekly'];
        }

        // News articles
        foreach (News::query()->where('status', 'published')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $article) {
            $urls[] = [route('news.show', $article), $article->updated_at, '0.7', 'weekly'];
        }

        // Videos
        foreach (Video::query()->whereNotNull('published_at')->where('published_at', '<=', now())->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $video) {
            $urls[] = [route('video.show', $video), $video->updated_at, '0.6', 'weekly'];
        }

        // Service centers
        foreach (ServiceProvider::query()->select(['id', 'updated_at'])->orderBy('id')->get() as $service) {
            $urls[] = [route('servicecenter.show', $service), $service->updated_at, '0.7', 'weekly'];
        }

        return $urls;
    }

    private function canonicalUrl(string $url): string
    {
        $canonicalBase = rtrim(config('app.canonical_url') ?: config('app.url') ?: 'https://motrix.uz', '/');
        
        // If canonicalBase is local IP or ngrok in dev, fallback to https://motrix.uz if desired, but prioritize production host
        if (str_contains($canonicalBase, '192.168.') || str_contains($canonicalBase, '127.0.0.1') || str_contains($canonicalBase, 'localhost')) {
            $canonicalBase = 'https://motrix.uz';
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';

        return $canonicalBase . $path . $query;
    }

    private function appendUrl(\DOMDocument $dom, \DOMElement $urlset, string $loc, ?Carbon $lastmod, string $priority = '0.5', string $changefreq = 'weekly'): void
    {
        $url = $dom->createElement('url');
        $url->appendChild($dom->createElement('loc', $loc));

        if ($lastmod) {
            $url->appendChild($dom->createElement('lastmod', $lastmod->toAtomString()));
        }

        $url->appendChild($dom->createElement('changefreq', $changefreq));
        $url->appendChild($dom->createElement('priority', $priority));

        $urlset->appendChild($url);
    }
}
