<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.viewAny');
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->can('users.viewAny');
    }

    public function create(User $user): bool
    {
        return $user->can('users.manage');
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->can('users.manage');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->can('users.manage');
    }

    public function manageRoles(User $user, User $model): bool
    {
        return $user->can('users.roles.manage');
    }
}
