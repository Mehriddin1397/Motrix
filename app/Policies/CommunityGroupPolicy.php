<?php

namespace App\Policies;

use App\Models\User;
use Modules\Community\Models\CommunityGroup;

class CommunityGroupPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, CommunityGroup $group): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('community.moderate');
    }

    public function update(User $user, CommunityGroup $group): bool
    {
        return $user->can('community.moderate');
    }

    public function delete(User $user, CommunityGroup $group): bool
    {
        return $user->can('community.moderate');
    }
}
