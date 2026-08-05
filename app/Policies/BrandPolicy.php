<?php

namespace App\Policies;

use App\Models\User;
use Modules\Brand\Models\Brand;

class BrandPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Brand $brand): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('motorcycles.brands.manage');
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->can('motorcycles.brands.manage');
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->can('motorcycles.brands.manage');
    }
}
