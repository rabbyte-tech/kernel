<?php

namespace App\Services\Packages;

use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Support\Facades\Schema;
use RabbyteTech\Contracts\Packages\PackageRegistry as PackageRegistryContract;

class PackageRegistry implements PackageRegistryContract
{
    public function enabled(string $name): bool
    {
        return Package::query()
            ->where('name', $name)
            ->where('status', PackageStatus::Enabled->value)
            ->exists();
    }

    /**
     * @return array<int, class-string>
     */
    public function enabledFilamentResources(): array
    {
        $resources = $this->filterEnabledManifests('filament_resources');

        return array_values(array_filter(
            $resources,
            static fn (string $resource): bool => class_exists($resource),
        ));
    }

    /**
     * @return array<int, string>
     */
    public function enabledApiRouteFiles(): array
    {
        return array_values(array_filter($this->filterEnabledManifests('routes_api')));
    }

    /**
     * @return array<int, string>
     */
    public function enabledNames(): array
    {
        return $this->enabledPackageNames();
    }

    /**
     * @return array<int, string>
     */
    private function enabledPackageNames(): array
    {
        if (! Schema::hasTable('packages')) {
            return [];
        }

        return Package::query()
            ->where('status', PackageStatus::Enabled->value)
            ->pluck('name')
            ->all();
    }

    /**
     * @param  'filament_resources'|'routes_api'  $key
     * @return array<int, mixed>
     */
    private function filterEnabledManifests(string $key): array
    {
        $enabledNames = $this->enabledPackageNames();

        if ($enabledNames === []) {
            return [];
        }

        $manifests = app(PackageDiscovery::class)->discover(base_path('packages'));
        $results = [];

        foreach ($manifests as $manifest) {
            if (! in_array($manifest['name'], $enabledNames, true)) {
                continue;
            }

            $value = $manifest[$key] ?? null;

            if (is_array($value)) {
                $results = array_merge($results, $value);

                continue;
            }

            if ($value !== null) {
                $results[] = $value;
            }
        }

        return array_values(array_unique($results));
    }
}
