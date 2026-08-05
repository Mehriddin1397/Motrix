<?php

namespace App\Policies;

use App\Models\User;
use Modules\Market\Models\Listing;

class ListingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Listing $listing): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('listings.create');
    }

    public function update(User $user, Listing $listing): bool
    {
        return ($listing->user_id === $user->id && $user->can('listings.update'))
            || $user->can('listings.moderate');
    }

    public function delete(User $user, Listing $listing): bool
    {
        return ($listing->user_id === $user->id && $user->can('listings.delete'))
            || $user->can('listings.moderate');
    }

    public function publish(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id && $user->can('listings.publish');
    }

    public function promote(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id && $user->can('listings.promote');
    }

    public function moderate(User $user, Listing $listing): bool
    {
        return $user->can('listings.moderate');
    }
}
