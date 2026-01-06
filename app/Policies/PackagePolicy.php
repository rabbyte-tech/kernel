<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('packages.viewAny');
    }

    public function view(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('packages.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('packages.create');
    }

    public function update(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('packages.update');
    }

    public function delete(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('packages.delete');
    }
}
