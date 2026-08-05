<?php

namespace App\Concerns;

use App\Models\AdPromotion;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Applied to any advertisable model (Listing, Part) to attach it to the
 * standard/premium/top/vip promotion system defined in config('access.promotions').
 */
trait HasAdPromotions
{
    public function promotions(): MorphMany
    {
        return $this->morphMany(AdPromotion::class, 'promotable');
    }

    public function activePromotion(): MorphOne
    {
        return $this->morphOne(AdPromotion::class, 'promotable')
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function isPromoted(): bool
    {
        return $this->promotion_tier !== 'standard'
            && $this->promoted_until
            && $this->promoted_until->isFuture();
    }
}
