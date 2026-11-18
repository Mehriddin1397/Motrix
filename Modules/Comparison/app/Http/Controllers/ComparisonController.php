<?php

namespace Modules\Comparison\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Brand\Models\Brand;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;

class ComparisonController extends Controller
{
    public function index(Request $request)
    {
        // 1. Process selected IDs
        $idsParam = $request->query('ids', '');
        $selectedIds = collect(is_array($idsParam) ? $idsParam : explode(',', (string) $idsParam))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(4)
            ->values()
            ->all();

        // 2. Fetch selected motorcycles in exact order
        $motorcycles = collect();
        if (! empty($selectedIds)) {
            $motorcycles = Motorcycle::query()
                ->with(['brand', 'category', 'specification', 'media'])
                ->whereIn('id', $selectedIds)
                ->get()
                ->sortBy(fn ($motorcycle) => array_search($motorcycle->id, $selectedIds))
                ->values();
        }

        // 3. Available motorcycles for the picker / modal
        $availableMotorcycles = Motorcycle::query()
            ->with(['brand:id,name', 'category:id,name', 'specification'])
            ->select(['id', 'brand_id', 'category_id', 'name', 'slug', 'year_start', 'year_end'])
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->whereHas('motorcycles')
            ->orderBy('name')
            ->get(['id', 'name']);

        // 4. Popular / suggested pairs for quick 1-click comparison
        $popularPairs = [];
        $categories = MotorcycleCategory::query()
            ->with(['motorcycles' => fn ($q) => $q->with(['brand', 'specification'])->take(4)])
            ->has('motorcycles', '>=', 2)
            ->take(4)
            ->get();

        foreach ($categories as $cat) {
            if ($cat->motorcycles->count() >= 2) {
                $popularPairs[] = [
                    'category' => $cat->name,
                    'moto1' => $cat->motorcycles[0],
                    'moto2' => $cat->motorcycles[1],
                ];
            }
        }

        return view('comparison::index', compact(
            'motorcycles',
            'selectedIds',
            'availableMotorcycles',
            'brands',
            'popularPairs'
        ));
    }
}
