<?php

namespace App\Policies;

use App\Models\User;
use Modules\Video\Models\Video;

class VideoPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Video $video): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('motorcycles.videos.manage');
    }

    public function update(User $user, Video $video): bool
    {
        return $user->can('motorcycles.videos.manage');
    }

    public function delete(User $user, Video $video): bool
    {
        return $user->can('motorcycles.videos.manage');
    }
}
