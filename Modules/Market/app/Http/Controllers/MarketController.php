<?php

namespace Modules\Market\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Modules\Brand\Models\Brand;
use Modules\Market\Models\Listing;
use Modules\Motorcycle\Models\Motorcycle;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        $listings = Listing::query()
            ->with(['motorcycle.brand', 'brand', 'city'])
            ->where('status', 'active')
            ->when($request->motorcycle, fn ($query, $slug) => $query->whereHas('motorcycle', fn ($m) => $m->where('slug', $slug)))
            ->when($request->city, fn ($query, $city) => $query->whereHas('city', fn ($c) => $c->where('id', $city)))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('market::index', compact('listings'));
    }

    public function create()
    {
        $this->authorize('create', Listing::class);

        return view('market::create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $this->authorize('create', Listing::class);

        $data = $this->validated($request);

        $listing = new Listing($data);
        $listing->user_id = $request->user()->id;
        $listing->status = Listing::determineInitialStatus($request->user());
        $listing->published_at = $listing->status === 'active' ? now() : null;
        $listing->save();

        $this->attachImages($request, $listing);

        return redirect()
            ->route('market.show', $listing)
            ->with('status', $listing->status === 'active'
                ? "E'loningiz chop etildi."
                : "E'loningiz qabul qilindi va moderator tekshiruvidan o'tmoqda.");
    }

    public function show(Listing $market)
    {
        $market->load(['motorcycle.brand', 'brand', 'city', 'user']);

        return view('market::show', ['listing' => $market]);
    }

    public function edit(Listing $market)
    {
        $this->authorize('update', $market);

        return view('market::edit', $this->formOptions() + ['listing' => $market]);
    }

    public function update(Request $request, Listing $market)
    {
        $this->authorize('update', $market);

        $market->fill($this->validated($request));
        $market->save();

        $this->attachImages($request, $market);

        return redirect()
            ->route('market.show', $market)
            ->with('status', "E'lon ma'lumotlari yangilandi.");
    }

    public function destroy(Listing $market)
    {
        $this->authorize('delete', $market);

        $market->delete();

        return redirect()
            ->route('profile.edit')
            ->with('status', "E'lon o'chirildi.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'motorcycle_id' => ['nullable', 'exists:motorcycles,id'],
            'custom_title' => ['nullable', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'in:USD,UZS'],
            'mileage_km' => ['required', 'integer', 'min:0'],
            'condition' => ['required', 'in:new,used'],
            'city_id' => ['required', 'exists:cities,id'],
            'description' => ['required', 'string'],
        ]);
    }

    private function attachImages(Request $request, Listing $listing): void
    {
        foreach ($request->file('images', []) as $image) {
            if ($image?->isValid()) {
                $listing->addMedia($image)->toMediaCollection('images');
            }
        }
    }

    private function formOptions(): array
    {
        return [
            'brands' => Brand::query()->orderBy('name')->get(),
            'motorcycles' => Motorcycle::query()->with('brand')->orderBy('name')->get(),
            'cities' => City::query()->orderBy('name')->get(),
        ];
    }
}
