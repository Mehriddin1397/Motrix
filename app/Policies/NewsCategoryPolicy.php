<?php

namespace App\Policies;

use App\Models\User;
use Modules\News\Models\NewsCategory;

class NewsCategoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, NewsCategory $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('news.manage');
    }

    public function update(User $user, NewsCategory $category): bool
    {
        return $user->can('news.manage');
    }

    public function delete(User $user, NewsCategory $category): bool
    {
        return $user->can('news.manage');
    }
}
