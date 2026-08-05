<?php

namespace App\Policies;

use App\Models\User;
use Modules\Community\Models\Comment;

class CommentPolicy
{
    public function create(User $user): bool
    {
        return $user->can('community.post');
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id || $user->can('community.moderate');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id || $user->can('community.moderate');
    }
}
