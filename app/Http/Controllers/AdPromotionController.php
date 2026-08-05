<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Market\Models\Listing;
use Modules\Parts\Models\Part;

class AdPromotionController extends Controller
{
    /**
     * @var array<string, class-string>
     */
    private const TYPES = [
        'listing' => Listing::class,
        'part' => Part::class,
    ];

    public function store(Request $request)
    {
        $data = $request->validate([
            'promotable_type' => ['required', 'in:listing,part'],
            'promotable_id' => ['required', 'integer'],
            'tier' => ['required', 'in:standard,premium,top,vip'],
        ]);

        $modelClass = self::TYPES[$data['promotable_type']];
        $promotable = $modelClass::findOrFail($data['promotable_id']);

        $this->authorize('promote', $promotable);

        $tierConfig = config("access.promotions.tiers.{$data['tier']}");

        $promotable->promotions()->create([
            'user_id' => $request->user()->id,
            'tier' => $data['tier'],
            'status' => 'pending_payment',
            'price' => $tierConfig['price'] ?? 0,
            'currency' => 'USD',
        ]);

        return back()->with('status', "So'rovingiz qabul qilindi. To'lov tizimi hali ulanmagan — tez orada ishga tushadi.");
    }
}
