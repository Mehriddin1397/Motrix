<?php

namespace App\Policies;

use App\Models\User;
use Modules\Community\Models\Post;

class PostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('community.post');
    }

    public function update(User $user, Post $post): bool
    {
        return $post->user_id === $user->id || $user->can('community.moderate');
    }

    public function delete(User $user, Post $post): bool
    {
        return $post->user_id === $user->id || $user->can('community.moderate');
    }
}
