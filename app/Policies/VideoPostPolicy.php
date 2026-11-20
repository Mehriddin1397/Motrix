<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VideoPost;

class VideoPostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, VideoPost $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, VideoPost $post): bool
    {
        return true;
    }

    public function delete(User $user, VideoPost $post): bool
    {
        return true;
    }
}
