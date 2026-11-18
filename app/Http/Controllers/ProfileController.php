<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\City;
use App\Models\SavedItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Modules\Market\Models\Conversation;
use Modules\Market\Models\Listing;
use Modules\Parts\Models\Part;

class ProfileController extends Controller
{
    /**
     * Display the user's profile / seller dashboard.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $trustProfile = $user->trustProfile()->firstOrCreate([], ['status' => 'new']);

        $listings = null;
        $listingStats = null;
        $conversations = null;

        if ($user->can('listings.create') || $user->isMotoSeller()) {
            $listings = Listing::query()
                ->where('user_id', $user->id)
                ->with(['motorcycle.brand', 'brand', 'city', 'media'])
                ->latest()
                ->limit(10)
                ->get();

            $listingStats = [
                'active' => Listing::where('user_id', $user->id)->where('status', 'active')->count(),
                'ended' => Listing::where('user_id', $user->id)->whereIn('status', ['sold', 'expired'])->count(),
                'views' => (int) Listing::where('user_id', $user->id)->sum('views_count'),
                'saved' => SavedItem::where('saveable_type', Listing::class)
                    ->whereIn('saveable_id', Listing::where('user_id', $user->id)->pluck('id'))
                    ->count(),
            ];

            $conversations = Conversation::query()
                ->where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))
                ->with(['listing.motorcycle', 'buyer', 'seller', 'messages' => fn ($q) => $q->latest()->limit(1)])
                ->withCount(['messages as unread_count' => function ($query) use ($user) {
                    $query->whereNull('read_at')->where('sender_id', '!=', $user->id);
                }])
                ->latest('updated_at')
                ->limit(5)
                ->get();
        }

        $parts = null;
        $partStats = null;

        if ($user->isPartsSeller()) {
            $parts = Part::query()
                ->where('seller_id', $user->id)
                ->with('category')
                ->latest()
                ->limit(10)
                ->get();

            $partStats = [
                'active' => Part::where('seller_id', $user->id)->where('status', 'active')->count(),
                'sold_out' => Part::where('seller_id', $user->id)->where('status', 'sold_out')->count(),
                'stock' => (int) Part::where('seller_id', $user->id)->sum('stock_qty'),
            ];
        }

        $promotions = null;

        if ($user->can('listings.create') || $user->isMotoSeller() || $user->isPartsSeller()) {
            $promotions = $user->adPromotions()->with('promotable')->latest()->limit(10)->get();
        }

        $userShop = $user->shop;
        $shopRequest = $user->shopRequests()->latest()->first();

        return view('profile.edit', [
            'user' => $user,
            'cities' => City::query()->orderBy('name')->get(),
            'trustProfile' => $trustProfile,
            'listings' => $listings,
            'listingStats' => $listingStats,
            'conversations' => $conversations,
            'parts' => $parts,
            'partStats' => $partStats,
            'promotions' => $promotions,
            'promotionTiers' => config('access.promotions.tiers'),
            'userShop' => $userShop,
            'shopRequest' => $shopRequest,
        ]);
    }

    /**
     * Display the public profile of a seller.
     */
    public function show(User $user): View
    {
        $user->load(['city', 'trustProfile', 'shops', 'shop.city']);

        $listings = Listing::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['motorcycle.brand', 'brand', 'city', 'media'])
            ->latest('published_at')
            ->paginate(12);

        $totalViews = (int) Listing::where('user_id', $user->id)->sum('views_count');
        $activeCount = Listing::where('user_id', $user->id)->where('status', 'active')->count();
        $soldCount = Listing::where('user_id', $user->id)->where('status', 'sold')->count();

        return view('profile.show', [
            'seller' => $user,
            'listings' => $listings,
            'totalViews' => $totalViews,
            'activeCount' => $activeCount,
            'soldCount' => $soldCount,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->boolean('remove_avatar')) {
            $user->clearMediaCollection('avatar');
            $user->avatar = null;
        } elseif ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar');
            $media = $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            $user->avatar = $media->getUrl();
        }

        unset($validated['avatar'], $validated['remove_avatar']);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
