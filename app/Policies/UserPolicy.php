<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.viewAny');
    }

    public function view(User $user, User $targetUser): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->hasPermissionTo('users.update');
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->hasPermissionTo('users.delete');
    }
}
