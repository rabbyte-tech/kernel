<?php

namespace App\Services\Events;

use App\Services\Packages\PackageDiscovery;
use App\Services\Packages\PackageRegistry;

readonly class EventRegistry
{
    public function __construct(
        private PackageDiscovery $packageDiscovery,
        private PackageRegistry $packageRegistry
    ) {}

    /**
     * @return array<string, array{package: string, version: string}>
     */
    public function events(): array
    {
        $events = [];

        foreach ($this->enabledManifests() as $manifest) {
            foreach ($manifest['events'] as $eventName) {
                $events[$eventName] = [
                    'package' => $manifest['name'],
                    'version' => $manifest['version'],
                ];
            }
        }

        return $events;
    }

    /**
     * @return array<string, array<int, class-string>>
     */
    public function bindings(): array
    {
        $bindings = [];

        foreach ($this->enabledManifests() as $manifest) {
            $listenerMap = $manifest['listener_map'];

            foreach ($manifest['listener_bindings'] as $eventName => $listenerKeys) {
                foreach ($listenerKeys as $listenerKey) {
                    $listener = $listenerMap[$listenerKey] ?? null;

                    if (! is_string($listener) || $listener === '') {
                        continue;
                    }

                    $bindings[$eventName][] = $listener;
                }
            }
        }

        foreach ($bindings as $eventName => $listeners) {
            $bindings[$eventName] = array_values(array_unique($listeners));
        }

        return $bindings;
    }

    /**
     * @return array{package: string, version: string}|null
     */
    public function definition(string $eventName): ?array
    {
        return $this->events()[$eventName] ?? null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function enabledManifests(): array
    {
        $enabledNames = $this->packageRegistry->enabledNames();

        if ($enabledNames === []) {
            return [];
        }

        $manifests = $this->packageDiscovery->discover(base_path('packages'));

        return array_values(array_filter(
            $manifests,
            static fn (array $manifest): bool => in_array($manifest['name'], $enabledNames, true),
        ));
    }
}
