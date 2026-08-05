<?php

namespace App\Policies;

use App\Models\User;
use Modules\Review\Models\MotorcycleReview;

class MotorcycleReviewPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, MotorcycleReview $review): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('reviews.create');
    }

    public function update(User $user, MotorcycleReview $review): bool
    {
        return $review->user_id === $user->id || $user->can('reviews.moderate');
    }

    public function delete(User $user, MotorcycleReview $review): bool
    {
        return $review->user_id === $user->id || $user->can('reviews.moderate');
    }
}
