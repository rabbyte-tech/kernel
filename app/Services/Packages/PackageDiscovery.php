<?php

namespace App\Services\Packages;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class PackageDiscovery
{
    private const VENDOR_MANIFEST_KEY = 'kernel';

    private const VENDOR_MANIFEST_PATH_KEY = 'manifest';

    /**
     * @return array<int, array{name: string, version: string, permissions: array<int, string>, mcp_entries: array<int, array{type: string, class: string, name: string, permission: string}>, manifest_hash: string, path: string, filament_resources: array<int, class-string>, routes_api: string|null, events: array<int, string>, listener_map: array<string, string>, listener_bindings: array<string, array<int, string>>}>
     */
    public function discover(string $packagesPath): array
    {
        if (! File::exists($packagesPath)) {
            $packagesPath = null;
        }

        $packages = [];

        if ($packagesPath !== null) {
            $packages = $this->discoverLocalManifests($packagesPath);
        }

        $vendorPackages = $this->discoverVendorManifests(base_path('vendor'));

        return $this->mergePackages($packages, $vendorPackages);
    }

    /**
     * @return array<int, array{name: string, version: string, permissions: array<int, string>, mcp_entries: array<int, array{type: string, class: string, name: string, permission: string}>, manifest_hash: string, path: string, filament_resources: array<int, class-string>, routes_api: string|null, events: array<int, string>, listener_map: array<string, string>, listener_bindings: array<string, array<int, string>>}>
     *
     * @throws FileNotFoundException
     */
    private function discoverLocalManifests(string $packagesPath): array
    {
        $manifests = File::glob($packagesPath.'/*/manifest.php') ?: [];
        $packages = [];

        foreach ($manifests as $manifestPath) {
            $manifest = require $manifestPath;

            if (! is_array($manifest)) {
                throw new InvalidArgumentException("Package manifest must return an array: {$manifestPath}");
            }

            $packages[] = $this->normalizeManifest($manifest, $manifestPath);
        }

        return $packages;
    }

    /**
     * @return array<int, array{name: string, version: string, permissions: array<int, string>, mcp_entries: array<int, array{type: string, class: string, name: string, permission: string}>, manifest_hash: string, path: string, filament_resources: array<int, class-string>, routes_api: string|null, events: array<int, string>, listener_map: array<string, string>, listener_bindings: array<string, array<int, string>>}>
     *
     * @throws FileNotFoundException
     */
    private function discoverVendorManifests(string $vendorPath): array
    {
        $installedPath = $vendorPath.'/composer/installed.json';

        if (! File::exists($installedPath)) {
            return [];
        }

        $installed = json_decode(File::get($installedPath), true);

        if (! is_array($installed)) {
            return [];
        }

        $packages = $installed['packages'] ?? $installed;

        if (! is_array($packages)) {
            return [];
        }

        $basePath = dirname($installedPath);
        $discovered = [];

        foreach ($packages as $package) {
            if (! is_array($package)) {
                continue;
            }

            $manifestPath = $this->resolveVendorManifestPath($package, $basePath);

            if ($manifestPath === null) {
                continue;
            }

            if (! File::exists($manifestPath)) {
                throw new InvalidArgumentException("Package manifest not found: {$manifestPath}");
            }

            $manifest = require $manifestPath;

            if (! is_array($manifest)) {
                throw new InvalidArgumentException("Package manifest must return an array: {$manifestPath}");
            }

            $discovered[] = $this->normalizeManifest($manifest, $manifestPath);
        }

        return $discovered;
    }

    /**
     * @param  array<string, mixed>  $package
     */
    private function resolveVendorManifestPath(array $package, string $installedBasePath): ?string
    {
        $extra = $package['extra'][self::VENDOR_MANIFEST_KEY] ?? null;
        $manifest = is_array($extra) ? ($extra[self::VENDOR_MANIFEST_PATH_KEY] ?? null) : null;

        if (! is_string($manifest) || $manifest === '') {
            return null;
        }

        $installPath = $package['install-path'] ?? null;

        if (! is_string($installPath) || $installPath === '') {
            return null;
        }

        if (! str_starts_with($installPath, DIRECTORY_SEPARATOR)) {
            $installPath = $installedBasePath.'/'.$installPath;
        }

        if (str_starts_with($manifest, DIRECTORY_SEPARATOR)) {
            return $manifest;
        }

        return rtrim($installPath, '/').'/'.ltrim($manifest, '/');
    }

    /**
     * @param  array<int, array{name: string, version: string, permissions: array<int, string>, mcp_entries: array<int, array{type: string, class: string, name: string, permission: string}>, manifest_hash: string, path: string, filament_resources: array<int, class-string>, routes_api: string|null, events: array<int, string>, listener_map: array<string, string>, listener_bindings: array<string, array<int, string>>}>  $local
     * @param  array<int, array{name: string, version: string, permissions: array<int, string>, mcp_entries: array<int, array{type: string, class: string, name: string, permission: string}>, manifest_hash: string, path: string, filament_resources: array<int, class-string>, routes_api: string|null, events: array<int, string>, listener_map: array<string, string>, listener_bindings: array<string, array<int, string>>}>  $vendor
     * @return array<int, array{name: string, version: string, permissions: array<int, string>, mcp_entries: array<int, array{type: string, class: string, name: string, permission: string}>, manifest_hash: string, path: string, filament_resources: array<int, class-string>, routes_api: string|null, events: array<int, string>, listener_map: array<string, string>, listener_bindings: array<string, array<int, string>>}>
     */
    private function mergePackages(array $local, array $vendor): array
    {
        $packages = [];

        foreach ($local as $package) {
            $packages[$package['name']] = $package;
        }

        foreach ($vendor as $package) {
            if (! array_key_exists($package['name'], $packages)) {
                $packages[$package['name']] = $package;
            }
        }

        return array_values($packages);
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @return array{name: string, version: string, permissions: array<int, string>, mcp_entries: array<int, array{type: string, class: string, name: string, permission: string}>, manifest_hash: string, path: string, filament_resources: array<int, class-string>, routes_api: string|null, events: array<int, string>, listener_map: array<string, string>, listener_bindings: array<string, array<int, string>>}
     *
     * @throws FileNotFoundException
     */
    private function normalizeManifest(array $manifest, string $manifestPath): array
    {
        $package = $manifest['package'] ?? null;

        if (! is_array($package)) {
            throw new InvalidArgumentException("Package manifest missing 'package' definition: {$manifestPath}");
        }

        $name = $package['name'] ?? null;
        $version = $package['version'] ?? null;

        if (! is_string($name) || $name === '') {
            throw new InvalidArgumentException("Package manifest missing 'package.name': {$manifestPath}");
        }

        if (! is_string($version) || $version === '') {
            throw new InvalidArgumentException("Package manifest missing 'package.version': {$manifestPath}");
        }

        $permissions = $manifest['permissions'] ?? [];

        if (! is_array($permissions)) {
            throw new InvalidArgumentException("Package manifest 'permissions' must be an array: {$manifestPath}");
        }

        $permissions = array_values(array_unique(array_filter($permissions, fn ($permission): bool => is_string($permission) && $permission !== '')));

        $mcpEntries = $this->normalizeMcpEntries($manifest['mcp'] ?? [], $manifestPath);

        $filament = $manifest['filament'] ?? [];
        $filamentResources = [];

        if (is_array($filament)) {
            $resources = $filament['resources'] ?? [];

            if (is_array($resources)) {
                $filamentResources = array_values(array_unique(array_filter($resources, fn ($resource): bool => is_string($resource) && $resource !== '')));
            }
        }

        $routes = $manifest['routes'] ?? [];
        $routesApi = null;

        if (is_array($routes)) {
            $routesApi = $routes['api'] ?? null;
        }

        if ($routesApi !== null) {
            if (! is_string($routesApi) || $routesApi === '') {
                throw new InvalidArgumentException("Package manifest 'routes.api' must be a string path: {$manifestPath}");
            }

            $routesApi = dirname($manifestPath).'/'.$routesApi;
        }

        $manifestHash = hash('sha256', File::get($manifestPath));
        $events = $manifest['events'] ?? [];

        if (! is_array($events)) {
            throw new InvalidArgumentException("Package manifest 'events' must be an array: {$manifestPath}");
        }

        $events = array_values(array_unique(array_filter($events, fn ($event): bool => is_string($event) && $event !== '')));

        $listeners = $manifest['listeners'] ?? [];
        $listenerMap = [];
        $listenerBindings = [];

        if (! is_array($listeners)) {
            throw new InvalidArgumentException("Package manifest 'listeners' must be an array: {$manifestPath}");
        }

        $map = $listeners['map'] ?? [];

        if (! is_array($map)) {
            throw new InvalidArgumentException("Package manifest 'listeners.map' must be an array: {$manifestPath}");
        }

        foreach ($map as $key => $listener) {
            if (! is_string($key) || $key === '' || ! is_string($listener) || $listener === '') {
                continue;
            }

            $listenerMap[$key] = $listener;
        }

        $bindings = $listeners['bindings'] ?? [];

        if (! is_array($bindings)) {
            throw new InvalidArgumentException("Package manifest 'listeners.bindings' must be an array: {$manifestPath}");
        }

        foreach ($bindings as $event => $keys) {
            if (! is_string($event) || $event === '' || ! is_array($keys)) {
                continue;
            }

            $keys = array_values(array_unique(array_filter($keys, fn ($key): bool => is_string($key) && $key !== '')));

            if ($keys === []) {
                continue;
            }

            $listenerBindings[$event] = $keys;
        }

        return [
            'name' => $name,
            'version' => $version,
            'permissions' => $permissions,
            'mcp_entries' => $mcpEntries,
            'manifest_hash' => $manifestHash,
            'path' => dirname($manifestPath),
            'filament_resources' => $filamentResources,
            'routes_api' => $routesApi,
            'events' => $events,
            'listener_map' => $listenerMap,
            'listener_bindings' => $listenerBindings,
        ];
    }

    /**
     * @param  array<string, mixed>  $mcp
     * @return array<int, array{type: string, class: string, name: string, permission: string}>
     */
    private function normalizeMcpEntries(array $mcp, string $manifestPath): array
    {
        if (! is_array($mcp)) {
            throw new InvalidArgumentException("Package manifest 'mcp' must be an array: {$manifestPath}");
        }

        $entries = [];

        foreach ([
            'tools' => 'tool',
            'resources' => 'resource',
            'prompts' => 'prompt',
        ] as $key => $type) {
            $items = $mcp[$key] ?? [];

            if (! is_array($items)) {
                throw new InvalidArgumentException("Package manifest 'mcp.{$key}' must be an array: {$manifestPath}");
            }

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $class = $item['class'] ?? null;
                $name = $item['name'] ?? null;
                $permission = $item['permission'] ?? null;

                if (! is_string($class) || $class === '' || ! is_string($name) || $name === '' || ! is_string($permission) || $permission === '') {
                    continue;
                }

                $entries[] = [
                    'type' => $type,
                    'class' => $class,
                    'name' => $name,
                    'permission' => $permission,
                ];
            }
        }

        return $entries;
    }
}
