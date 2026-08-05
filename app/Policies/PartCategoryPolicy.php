<?php

namespace App\Policies;

use App\Models\User;
use Modules\Parts\Models\PartCategory;

class PartCategoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, PartCategory $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('parts-categories.manage');
    }

    public function update(User $user, PartCategory $category): bool
    {
        return $user->can('parts-categories.manage');
    }

    public function delete(User $user, PartCategory $category): bool
    {
        return $user->can('parts-categories.manage');
    }
}
