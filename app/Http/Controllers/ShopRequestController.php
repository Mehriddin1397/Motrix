<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\ShopRequest;
use App\Services\EskizSmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopRequestController extends Controller
{
    /**
     * Show the shop application form.
     */
    public function create(): View
    {
        $existingRequest = auth()->user()->shopRequests()->latest()->first();

        return view('shops.request', [
            'cities' => City::orderBy('name')->get(),
            'existingRequest' => $existingRequest,
        ]);
    }

    /**
     * Store a new shop application for admin review.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_type' => ['required', 'in:moto,parts,both'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'message' => ['nullable', 'string', 'max:1500'],
        ]);

        $shopRequest = new ShopRequest($validated);
        $shopRequest->user_id = $request->user()->id;
        $shopRequest->status = 'pending';
        $shopRequest->save();

        // Adminga yangi ariza kelganligi haqida SMS yuborish (+998942551397)
        app(EskizSmsService::class)->notifyAdminNewRequest($shopRequest);

        return redirect()->back()->with('status', 'Do\'kon ochish arizangiz adminga muvaffaqiyatli yuborildi! Tez orada moderator yoki admin tomonidan ko\'rib chiqiladi.');
    }
}
