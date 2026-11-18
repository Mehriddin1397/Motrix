<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Parts\Models\Part;
use Modules\ServiceCenter\Models\ServiceProvider;

class SearchController extends Controller
{
    /**
     * Handle unified global search across motorcycles, market listings, workshops, and parts.
     */
    public function index(Request $request): View|JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        $tab = (string) $request->input('tab', 'all');

        if (! in_array($tab, ['all', 'motorcycles', 'market', 'services', 'parts'], true)) {
            $tab = 'all';
        }

        $motorcyclesQuery = Motorcycle::query()
            ->with(['brand', 'category', 'media', 'specification']);

        $marketQuery = Listing::query()
            ->where('status', 'active')
            ->with(['brand', 'motorcycle.brand', 'city', 'media', 'shop']);

        $servicesQuery = ServiceProvider::query()
            ->with(['category', 'city', 'media']);

        $partsQuery = Part::query()
            ->where('status', 'active')
            ->with(['category', 'media', 'shop']);

        if ($q !== '') {
            $motorcyclesQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('generation', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"));
            });

            $marketQuery->where(function ($query) use ($q) {
                $query->where('custom_title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('motorcycle', fn ($m) => $m->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('city', fn ($c) => $c->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('shop', fn ($s) => $s->where('name', 'like', "%{$q}%"));
            });

            $servicesQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhereHas('city', fn ($c) => $c->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"));
            });

            $partsQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('part_number', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('motorcycles', fn ($m) => $m->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('shop', fn ($s) => $s->where('name', 'like', "%{$q}%"));
            });
        }

        // Calculate counts
        $counts = [
            'motorcycles' => $q !== '' ? (clone $motorcyclesQuery)->count() : 0,
            'market' => $q !== '' ? (clone $marketQuery)->count() : 0,
            'services' => $q !== '' ? (clone $servicesQuery)->count() : 0,
            'parts' => $q !== '' ? (clone $partsQuery)->count() : 0,
        ];
        $totalCount = array_sum($counts);

        // AJAX / Live JSON search
        if ($request->wantsJson() || $request->has('live')) {
            return response()->json([
                'query' => $q,
                'total' => $totalCount,
                'counts' => $counts,
                'motorcycles' => $q !== '' ? (clone $motorcyclesQuery)->latest('id')->limit(4)->get() : [],
                'market' => $q !== '' ? (clone $marketQuery)->latest('published_at')->limit(4)->get() : [],
                'services' => $q !== '' ? (clone $servicesQuery)->latest('id')->limit(4)->get() : [],
                'parts' => $q !== '' ? (clone $partsQuery)->latest('id')->limit(4)->get() : [],
            ]);
        }

        // Prepare results based on selected tab
        $results = [
            'motorcycles' => null,
            'market' => null,
            'services' => null,
            'parts' => null,
        ];

        if ($q !== '') {
            if ($tab === 'motorcycles') {
                $results['motorcycles'] = $motorcyclesQuery->latest('id')->paginate(12)->withQueryString();
            } elseif ($tab === 'market') {
                $results['market'] = $marketQuery->latest('published_at')->paginate(12)->withQueryString();
            } elseif ($tab === 'services') {
                $results['services'] = $servicesQuery->latest('id')->paginate(12)->withQueryString();
            } elseif ($tab === 'parts') {
                $results['parts'] = $partsQuery->latest('id')->paginate(12)->withQueryString();
            } else {
                // 'all' tab: preview top 4 for each category
                $results['motorcycles'] = $motorcyclesQuery->latest('id')->limit(4)->get();
                $results['market'] = $marketQuery->latest('published_at')->limit(4)->get();
                $results['services'] = $servicesQuery->latest('id')->limit(4)->get();
                $results['parts'] = $partsQuery->latest('id')->limit(4)->get();
            }
        }

        return view('search.index', [
            'query' => $q,
            'tab' => $tab,
            'counts' => $counts,
            'totalCount' => $totalCount,
            'results' => $results,
        ]);
    }
}
