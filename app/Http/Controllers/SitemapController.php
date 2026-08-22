<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\News\Models\News;
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

        foreach ($this->urls() as [$loc, $lastmod]) {
            $this->appendUrl($dom, $urlset, $loc, $lastmod);
        }

        return $dom->saveXML();
    }

    /**
     * @return list<array{0: string, 1: ?Carbon}>
     */
    private function urls(): array
    {
        $urls = [];

        // Static/listing pages: no single underlying row to date them, so no
        // <lastmod> is emitted rather than fabricating one.
        foreach ([
            'home',
            'motorcycle.index',
            'market.index',
            'parts.index',
            'video.index',
            'news.index',
            'comparison.index',
            'servicecenter.index',
        ] as $route) {
            $urls[] = [route($route), null];
        }

        foreach (Motorcycle::query()->where('status', 'published')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $motorcycle) {
            $urls[] = [route('motorcycle.show', $motorcycle), $motorcycle->updated_at];
        }

        foreach (Listing::query()->where('status', 'active')->select(['id', 'updated_at'])->orderBy('id')->get() as $listing) {
            $urls[] = [route('market.show', $listing), $listing->updated_at];
        }

        foreach (Part::query()->where('status', 'active')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $part) {
            $urls[] = [route('parts.show', $part), $part->updated_at];
        }

        foreach (Video::query()->whereNotNull('published_at')->where('published_at', '<=', now())->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $video) {
            $urls[] = [route('video.show', $video), $video->updated_at];
        }

        foreach (News::query()->where('status', 'published')->select(['id', 'slug', 'updated_at'])->orderBy('id')->get() as $article) {
            $urls[] = [route('news.show', $article), $article->updated_at];
        }

        // No status/approval field: every row is public as soon as it exists.
        foreach (ServiceProvider::query()->select(['id', 'updated_at'])->orderBy('id')->get() as $service) {
            $urls[] = [route('servicecenter.show', $service), $service->updated_at];
        }

        return $urls;
    }

    private function appendUrl(\DOMDocument $dom, \DOMElement $urlset, string $loc, ?Carbon $lastmod): void
    {
        $url = $dom->createElement('url');
        $url->appendChild($dom->createElement('loc', $loc));

        if ($lastmod) {
            $url->appendChild($dom->createElement('lastmod', $lastmod->toAtomString()));
        }

        $urlset->appendChild($url);
    }
}
