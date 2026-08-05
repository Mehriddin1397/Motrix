<?php

namespace App\Policies;

use App\Models\User;
use Modules\News\Models\News;

class NewsPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, News $news): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('news.manage');
    }

    public function update(User $user, News $news): bool
    {
        return $user->can('news.manage');
    }

    public function delete(User $user, News $news): bool
    {
        return $user->can('news.manage');
    }
}
