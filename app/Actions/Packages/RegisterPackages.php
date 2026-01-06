<?php

namespace App\Actions\Packages;

use App\Enums\PackageStatus;
use App\Models\McpEntry;
use App\Models\Package;
use App\Services\Packages\PackageDiscovery;
use Spatie\Permission\Models\Permission;

class RegisterPackages
{
    public function __construct(public PackageDiscovery $discovery) {}

    /**
     * @return array{registered: int, updated: int, disabled: int}
     */
    public function execute(): array
    {
        $packagesPath = base_path('packages');
        $manifests = $this->discovery->discover($packagesPath);

        $registered = 0;
        $updated = 0;
        $disabled = 0;
        $names = [];

        foreach ($manifests as $manifest) {
            $names[] = $manifest['name'];

            $package = Package::query()->where('name', $manifest['name'])->first();

            if ($package) {
                $package->fill([
                    'version' => $manifest['version'],
                    'manifest_hash' => $manifest['manifest_hash'],
                ]);

                if ($package->isDirty()) {
                    $package->save();
                    $updated++;
                }
            } else {
                $package = Package::query()->create([
                    'name' => $manifest['name'],
                    'version' => $manifest['version'],
                    'status' => PackageStatus::Installed->value,
                    'manifest_hash' => $manifest['manifest_hash'],
                ]);
                $registered++;
            }

            $permissions = array_merge(
                $manifest['permissions'],
                array_column($manifest['mcp_entries'], 'permission'),
            );

            $permissions = array_values(array_unique(array_filter($permissions)));

            $this->syncPermissions($permissions);

            if ($package) {
                $this->syncMcpEntries($package, $manifest['mcp_entries']);
            }
        }

        $names = array_values(array_unique($names));

        if ($names !== []) {
            $disabled = Package::query()
                ->whereNotIn('name', $names)
                ->where('status', '!=', PackageStatus::Disabled->value)
                ->update(['status' => PackageStatus::Disabled->value]);
        }

        return [
            'registered' => $registered,
            'updated' => $updated,
            'disabled' => $disabled,
        ];
    }

    /**
     * @param  array<int, string>  $permissions
     */
    private function syncPermissions(array $permissions): void
    {
        $guardName = config('auth.defaults.guard');

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }
    }

    /**
     * @param  array<int, array{type: string, class: string, name: string, permission: string}>  $entries
     */
    private function syncMcpEntries(Package $package, array $entries): void
    {
        $existing = McpEntry::query()
            ->where('package_id', $package->id)
            ->get()
            ->keyBy(fn (McpEntry $entry): string => $entry->type->value.'|'.$entry->name);

        $seen = [];

        foreach ($entries as $entry) {
            $key = $entry['type'].'|'.$entry['name'];
            $seen[] = $key;

            $record = $existing->get($key);

            if ($record) {
                $record->fill([
                    'class' => $entry['class'],
                    'permission' => $entry['permission'],
                ]);

                if ($record->isDirty()) {
                    $record->save();
                }

                continue;
            }

            McpEntry::query()->create([
                'package_id' => $package->id,
                'type' => $entry['type'],
                'name' => $entry['name'],
                'class' => $entry['class'],
                'permission' => $entry['permission'],
                'is_enabled' => true,
            ]);
        }

        $keysToDelete = array_diff($existing->keys()->all(), $seen);

        if ($keysToDelete !== []) {
            $ids = $existing->only($keysToDelete)->pluck('id')->all();

            McpEntry::query()->whereIn('id', $ids)->delete();
        }
    }
}
