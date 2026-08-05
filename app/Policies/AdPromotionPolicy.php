<?php

namespace App\Policies;

use App\Models\AdPromotion;
use App\Models\User;

class AdPromotionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('promotions.manage');
    }

    public function view(User $user, AdPromotion $promotion): bool
    {
        return $user->id === $promotion->user_id || $user->can('promotions.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('promotions.manage');
    }

    public function update(User $user, AdPromotion $promotion): bool
    {
        return $user->can('promotions.manage');
    }

    public function delete(User $user, AdPromotion $promotion): bool
    {
        return $user->can('promotions.manage');
    }
}
