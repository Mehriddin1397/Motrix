<?php

namespace App\Policies;

use App\Models\User;
use Modules\Motorcycle\Models\MotorcycleCategory;

class MotorcycleCategoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, MotorcycleCategory $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('motorcycles.categories.manage');
    }

    public function update(User $user, MotorcycleCategory $category): bool
    {
        return $user->can('motorcycles.categories.manage');
    }

    public function delete(User $user, MotorcycleCategory $category): bool
    {
        return $user->can('motorcycles.categories.manage');
    }
}
