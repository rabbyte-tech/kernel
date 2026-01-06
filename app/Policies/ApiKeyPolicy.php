<?php

namespace App\Policies;

use App\Models\ApiKey;
use App\Models\User;

class ApiKeyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('api-keys.viewAny');
    }

    public function view(User $user, ApiKey $apiKey): bool
    {
        return $user->hasPermissionTo('api-keys.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('api-keys.create');
    }

    public function update(User $user, ApiKey $apiKey): bool
    {
        return $user->hasPermissionTo('api-keys.update');
    }

    public function delete(User $user, ApiKey $apiKey): bool
    {
        return $user->hasPermissionTo('api-keys.delete');
    }
}
