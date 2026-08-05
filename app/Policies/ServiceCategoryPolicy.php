<?php

namespace App\Policies;

use App\Models\User;
use Modules\ServiceCenter\Models\ServiceCategory;

class ServiceCategoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ServiceCategory $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('service-centers.manage');
    }

    public function update(User $user, ServiceCategory $category): bool
    {
        return $user->can('service-centers.manage');
    }

    public function delete(User $user, ServiceCategory $category): bool
    {
        return $user->can('service-centers.manage');
    }
}
