<?php

namespace App\Policies;

use App\Models\User;
use Modules\Parts\Models\Part;

class PartPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Part $part): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('parts.create');
    }

    public function update(User $user, Part $part): bool
    {
        return ($part->seller_id === $user->id && $user->can('parts.update'))
            || $user->can('parts.moderate');
    }

    public function delete(User $user, Part $part): bool
    {
        return ($part->seller_id === $user->id && $user->can('parts.delete'))
            || $user->can('parts.moderate');
    }

    public function manageStock(User $user, Part $part): bool
    {
        return $part->seller_id === $user->id && $user->can('parts.manageStock');
    }

    public function promote(User $user, Part $part): bool
    {
        return $part->seller_id === $user->id && $user->can('parts.promote');
    }

    public function moderate(User $user, Part $part): bool
    {
        return $user->can('parts.moderate');
    }
}
