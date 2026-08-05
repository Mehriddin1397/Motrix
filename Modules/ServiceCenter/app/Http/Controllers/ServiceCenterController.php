<?php

namespace Modules\ServiceCenter\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ServiceCenter\Models\ServiceCategory;
use Modules\ServiceCenter\Models\ServiceProvider;

class ServiceCenterController extends Controller
{
    public function index(Request $request)
    {
        $providers = ServiceProvider::query()
            ->with(['category', 'city'])
            ->when($request->category, fn ($query, $slug) => $query->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($request->brand, fn ($query, $brand) => $query->whereHas('brands', fn ($b) => $b->where('slug', $brand)))
            ->orderByDesc('verified')
            ->orderByDesc('rating_avg')
            ->paginate(12)
            ->withQueryString();

        $categories = ServiceCategory::query()->orderBy('name')->get();

        return view('servicecenter::index', compact('providers', 'categories'));
    }

    public function create()
    {
        return view('servicecenter::create');
    }

    public function store(Request $request) {}

    public function show(ServiceProvider $service)
    {
        $service->load(['category', 'city', 'brands', 'reviews.user']);

        return view('servicecenter::show', ['provider' => $service]);
    }

    public function edit(ServiceProvider $service)
    {
        return view('servicecenter::edit', ['provider' => $service]);
    }

    public function update(Request $request, ServiceProvider $service) {}

    public function destroy(ServiceProvider $service) {}
}
