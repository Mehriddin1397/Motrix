<?php

namespace App\Policies;

use App\Models\User;
use Modules\ServiceCenter\Models\ServiceProvider;

class ServiceProviderPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ServiceProvider $serviceProvider): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('service-centers.manage');
    }

    public function update(User $user, ServiceProvider $serviceProvider): bool
    {
        return $user->can('service-centers.manage');
    }

    public function delete(User $user, ServiceProvider $serviceProvider): bool
    {
        return $user->can('service-centers.manage');
    }
}
