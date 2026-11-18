<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Display a shop's public storefront.
     */
    public function show(Shop $shop): View
    {
        abort_unless($shop->is_active, 404);

        $shop->incrementQuietly('views_count');
        $shop->load(['city', 'user']);

        $listings = $shop->listings()
            ->where('status', 'active')
            ->with(['motorcycle.brand', 'brand', 'city', 'media'])
            ->latest('published_at')
            ->paginate(12, ['*'], 'listings_page');

        $parts = $shop->parts()
            ->where('status', 'active')
            ->with(['category', 'media'])
            ->latest()
            ->paginate(12, ['*'], 'parts_page');

        return view('shops.show', [
            'shop' => $shop,
            'listings' => $listings,
            'parts' => $parts,
        ]);
    }

    /**
     * Update shop information and location coordinates (by shop owner).
     */
    public function update(Request $request, Shop $shop): RedirectResponse
    {
        $user = $request->user();

        // Check if user is the assigned shop owner or has administrator access
        if ($shop->user_id !== $user->id && ! $user->can('platform.access-admin')) {
            abort(403, 'Siz ushbu do\'konni tahrirlash huquqiga ega emassiz.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'telegram' => ['nullable', 'string', 'max:100'],
            'instagram' => ['nullable', 'string', 'max:100'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $shop->update($validated);

        return redirect()->back()->with('status', 'Do\'kon ma\'lumotlari va lokatsiyasi muvaffaqiyatli saqlandi.');
    }
}
