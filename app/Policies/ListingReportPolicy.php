<?php

namespace App\Policies;

use App\Models\User;
use Modules\Market\Models\ListingReport;

class ListingReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('reports.view');
    }

    public function view(User $user, ListingReport $report): bool
    {
        return $report->user_id === $user->id || $user->can('reports.view');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ListingReport $report): bool
    {
        return $user->can('listings.moderate');
    }

    public function delete(User $user, ListingReport $report): bool
    {
        return $user->can('listings.moderate');
    }

    public function resolve(User $user, ListingReport $report): bool
    {
        return $user->can('listings.moderate');
    }
}
