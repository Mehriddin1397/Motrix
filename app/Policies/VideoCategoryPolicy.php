<?php

namespace App\Policies;

use App\Models\User;
use Modules\Video\Models\VideoCategory;

class VideoCategoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, VideoCategory $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('motorcycles.videos.manage');
    }

    public function update(User $user, VideoCategory $category): bool
    {
        return $user->can('motorcycles.videos.manage');
    }

    public function delete(User $user, VideoCategory $category): bool
    {
        return $user->can('motorcycles.videos.manage');
    }
}
