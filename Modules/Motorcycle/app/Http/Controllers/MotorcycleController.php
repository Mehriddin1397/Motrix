<?php

namespace Modules\Motorcycle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;

class MotorcycleController extends Controller
{
    public function index(Request $request)
    {
        $motorcycles = Motorcycle::query()
            ->with(['brand', 'category', 'specification'])
            ->where('status', 'published')
            ->when($request->q, fn ($query, $q) => $query->where('name', 'like', "%{$q}%"))
            ->when($request->brand, fn ($query, $brand) => $query->whereHas('brand', fn ($b) => $b->where('slug', $brand)))
            ->when($request->category, fn ($query, $category) => $query->whereHas('category', fn ($c) => $c->where('slug', $category)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = MotorcycleCategory::query()->orderBy('name')->get();

        return view('motorcycle::index', compact('motorcycles', 'categories'));
    }

    public function show(Motorcycle $motorcycle)
    {
        $motorcycle->load([
            'brand',
            'category',
            'specification',
            'prosCons',
            'engineDetail',
            'relatedMotorcycles.brand',
            'relatedMotorcycles.specification',
            'reviews' => fn ($q) => $q->where('status', 'approved')->latest(),
            'reviews.user',
            'videos',
        ]);

        $motorcycle->increment('views_count');

        return view('motorcycle::show', compact('motorcycle'));
    }

    public function create()
    {
        $this->authorize('create', Motorcycle::class);

        return view('motorcycle::create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Motorcycle::class);
    }

    public function edit(Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);

        return view('motorcycle::edit', compact('motorcycle'));
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);
    }

    public function destroy(Motorcycle $motorcycle)
    {
        $this->authorize('delete', $motorcycle);
    }
}
