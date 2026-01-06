<?php

namespace App\Services\Authorization;

use App\Models\ApiKey;
use App\Services\Packages\PackagePermissionRegistry;
use Illuminate\Database\Eloquent\Model;
use RabbyteTech\Contracts\Authorization\AbilityResolver as AbilityResolverContract;

readonly class AbilityResolver implements AbilityResolverContract
{
    public function __construct(private PackagePermissionRegistry $packagePermissionRegistry) {}

    public function allows(?object $actor, string $ability): bool
    {
        if ($ability === '') {
            return false;
        }

        if (! $this->abilityExists($ability)) {
            return false;
        }

        if ($this->abilityBelongsToDisabledPackage($ability)) {
            return false;
        }

        if ($actor instanceof ApiKey) {
            if (! $actor->is_active) {
                return false;
            }

            return $actor->hasPermissionTo($ability);
        }

        if ($actor instanceof Model && method_exists($actor, 'hasPermissionTo')) {
            return $actor->hasPermissionTo($ability);
        }

        if ($actor instanceof Model && method_exists($actor, 'can')) {
            return $actor->can($ability);
        }

        return false;
    }

    private function abilityExists(string $ability): bool
    {
        $permissionModel = config('permission.models.permission');

        return $permissionModel::query()->where('name', $ability)->exists();
    }

    private function abilityBelongsToDisabledPackage(string $ability): bool
    {
        $enabled = $this->packagePermissionRegistry->enabledPermissions();

        if (in_array($ability, $enabled, true)) {
            return false;
        }

        $all = $this->packagePermissionRegistry->allPackagePermissions();

        return in_array($ability, $all, true);
    }
}
