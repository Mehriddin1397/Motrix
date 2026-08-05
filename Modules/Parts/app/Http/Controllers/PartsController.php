<?php

namespace Modules\Parts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Parts\Models\Part;
use Modules\Parts\Models\PartCategory;

class PartsController extends Controller
{
    public function index(Request $request)
    {
        $parts = Part::query()
            ->with(['category', 'seller'])
            ->where('status', 'active')
            ->when($request->category, fn ($query, $slug) => $query->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($request->motorcycle, fn ($query, $slug) => $query->whereHas('motorcycles', fn ($m) => $m->where('slug', $slug)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = PartCategory::query()->whereNull('parent_id')->orderBy('name')->get();

        return view('parts::index', compact('parts', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Part::class);

        return view('parts::create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $this->authorize('create', Part::class);

        $data = $this->validated($request);

        $part = new Part($data);
        $part->seller_id = $request->user()->id;
        $part->status = Part::determineInitialStatus($request->user());
        $part->save();

        $this->attachImages($request, $part);

        return redirect()
            ->route('parts.show', $part)
            ->with('status', $part->status === 'active'
                ? "Mahsulotingiz chop etildi."
                : "Mahsulotingiz qabul qilindi va moderator tekshiruvidan o'tmoqda.");
    }

    public function show(Part $part)
    {
        $part->load(['category', 'seller', 'motorcycles.brand']);

        return view('parts::show', compact('part'));
    }

    public function edit(Part $part)
    {
        $this->authorize('update', $part);

        return view('parts::edit', $this->formOptions() + ['part' => $part]);
    }

    public function update(Request $request, Part $part)
    {
        $this->authorize('update', $part);

        $part->fill($this->validated($request));
        $part->save();

        $this->attachImages($request, $part);

        return redirect()
            ->route('parts.show', $part)
            ->with('status', 'Mahsulot ma\'lumotlari yangilandi.');
    }

    public function destroy(Part $part)
    {
        $this->authorize('delete', $part);

        $part->delete();

        return redirect()
            ->route('profile.edit')
            ->with('status', "Mahsulot o'chirildi.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:part_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'part_type' => ['required', 'in:oem,aftermarket'],
            'part_number' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'condition' => ['required', 'in:new,used'],
            'description' => ['required', 'string'],
        ]);
    }

    private function attachImages(Request $request, Part $part): void
    {
        foreach ($request->file('images', []) as $image) {
            if ($image?->isValid()) {
                $part->addMedia($image)->toMediaCollection('images');
            }
        }
    }

    private function formOptions(): array
    {
        return [
            'categories' => PartCategory::query()->orderBy('name')->get(),
        ];
    }
}
