<?php

namespace App\Services\Packages;

readonly class PackagePermissionRegistry
{
    public function __construct(
        private PackageDiscovery $packageDiscovery,
        private PackageRegistry $packageRegistry
    ) {}

    /**
     * @return array<int, string>
     */
    public function enabledPermissions(): array
    {
        return $this->permissionsFor($this->packageRegistry->enabledNames());
    }

    /**
     * @return array<int, string>
     */
    public function allPackagePermissions(): array
    {
        return $this->permissionsFor(null);
    }

    /**
     * @param  array<int, string>|null  $enabledNames
     * @return array<int, string>
     */
    private function permissionsFor(?array $enabledNames): array
    {
        $manifests = $this->packageDiscovery->discover(base_path('packages'));
        $permissions = [];

        foreach ($manifests as $manifest) {
            if ($enabledNames !== null && ! in_array($manifest['name'], $enabledNames, true)) {
                continue;
            }

            $permissions = array_merge(
                $permissions,
                $manifest['permissions'],
                array_column($manifest['mcp_entries'], 'permission'),
            );
        }

        return array_values(array_unique(array_filter($permissions)));
    }
}
