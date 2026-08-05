<?php

namespace App\Policies;

use App\Models\User;
use Modules\Motorcycle\Models\Motorcycle;

class MotorcyclePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Motorcycle $motorcycle): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('motorcycles.models.manage');
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $user->can('motorcycles.models.manage');
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->can('motorcycles.models.manage');
    }
}
