<?php

namespace Modules\Motorcycle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Brand\Models\Brand;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;

class MotorcycleController extends Controller
{
    public function index(Request $request)
    {
        $query = Motorcycle::query()
            ->with(['brand', 'category', 'specification'])
            ->where('status', 'published');

        // Qidiruv (nomi, brendi yoki avlodi bo'yicha)
        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('generation', 'like', "%{$q}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$q}%"));
            });
        }

        // Brend filtri
        if ($brand = $request->input('brand')) {
            $query->whereHas('brand', fn ($b) => $b->where('slug', $brand));
        }

        // Kategoriya filtri
        if ($category = $request->input('category')) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $category));
        }

        // Dvigatel turi (elektr / benzin)
        if ($powertrain = $request->input('powertrain')) {
            if ($powertrain === 'electric') {
                $query->whereHas('specification', fn ($s) => $s->where('powertrain_type', 'electric'));
            } elseif ($powertrain === 'petrol') {
                $query->whereHas('specification', fn ($s) => $s->where('powertrain_type', '!=', 'electric')->orWhereNull('powertrain_type'));
            }
        }

        // Kubatura (dvigatel hajmi) filtri
        if ($displacement = $request->input('displacement')) {
            $query->whereHas('specification', function ($s) use ($displacement) {
                match ($displacement) {
                    '<150' => $s->where('displacement_cc', '<', 150),
                    '150-300' => $s->whereBetween('displacement_cc', [150, 300]),
                    '300-600' => $s->whereBetween('displacement_cc', [300, 600]),
                    '600-1000' => $s->whereBetween('displacement_cc', [600, 1000]),
                    '1000+' => $s->where('displacement_cc', '>=', 1000),
                    'electric' => $s->where('powertrain_type', 'electric'),
                    default => null,
                };
            });
        }

        // Yangi boshlovchilar uchun (beginner friendly)
        if ($request->boolean('beginner')) {
            $query->whereHas('specification', fn ($s) => $s->where('beginner_friendly', true));
        }

        // Saralash (Sort)
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'popular' => $query->orderByDesc('views_count')->orderByDesc('motorcycles.id'),
            'power' => $query->leftJoin('motorcycle_specifications', 'motorcycles.id', '=', 'motorcycle_specifications.motorcycle_id')
                             ->orderByDesc('motorcycle_specifications.horsepower')
                             ->select('motorcycles.*'),
            'displacement' => $query->leftJoin('motorcycle_specifications', 'motorcycles.id', '=', 'motorcycle_specifications.motorcycle_id')
                                   ->orderByDesc('motorcycle_specifications.displacement_cc')
                                   ->select('motorcycles.*'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->latest('motorcycles.id'),
        };

        $motorcycles = $query->paginate(24)->withQueryString();

        // Kategoriya va brendlarni faqat e'lon qilingan mototsikli borlari bilan yuklash
        $categories = MotorcycleCategory::query()
            ->withCount(['motorcycles' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->withCount(['motorcycles' => fn ($q) => $q->where('status', 'published')])
            ->has('motorcycles')
            ->orderBy('name')
            ->get();

        $displacementOptions = [
            '<150' => '150 cc gacha',
            '150-300' => '150 – 300 cc',
            '300-600' => '300 – 600 cc',
            '600-1000' => '600 – 1000 cc',
            '1000+' => '1000+ cc',
            'electric' => '⚡ Elektr',
        ];

        return view('motorcycle::index', compact(
            'motorcycles',
            'categories',
            'brands',
            'displacementOptions'
        ));
    }

    public function show(Motorcycle $motorcycle)
    {
        $motorcycle->incrementQuietly('views_count');

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
