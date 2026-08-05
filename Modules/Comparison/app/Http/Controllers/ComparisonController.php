<?php

namespace Modules\Comparison\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Motorcycle\Models\Motorcycle;

class ComparisonController extends Controller
{
    public function index(Request $request)
    {
        $ids = array_filter(explode(',', (string) $request->query('ids')));

        $motorcycles = Motorcycle::query()
            ->with(['brand', 'specification'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($motorcycle) => array_search($motorcycle->id, $ids));

        return view('comparison::index', compact('motorcycles'));
    }
}
