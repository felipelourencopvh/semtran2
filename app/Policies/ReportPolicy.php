<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('report.view');
    }

    public function view(User $user, Report $report): bool
    {
        return $user->can('report.view');
    }

    public function create(User $user): bool
    {
        return $user->can('report.create');
    }

    public function update(User $user, Report $report): bool
    {
        return $user->can('report.update');
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->can('report.delete');
    }

    public function restore(User $user, Report $report): bool
    {
        return $user->can('report.update');
    }

    public function forceDelete(User $user, Report $report): bool
    {
        return $user->can('report.delete');
    }
}
