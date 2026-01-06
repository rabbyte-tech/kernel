<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = config('auth.defaults.guard');

        $permissions = [
            'panel.access',

            'api-keys.viewAny',
            'api-keys.view',
            'api-keys.create',
            'api-keys.update',
            'api-keys.delete',

            'packages.viewAny',
            'packages.view',
            'packages.create',
            'packages.update',
            'packages.delete',

            'mcp-entries.viewAny',
            'mcp-entries.view',
            'mcp-entries.update',

            'webhook-subscriptions.viewAny',
            'webhook-subscriptions.view',
            'webhook-subscriptions.create',
            'webhook-subscriptions.update',
            'webhook-subscriptions.delete',

            'webhook-deliveries.viewAny',
            'webhook-deliveries.view',

            'users.viewAny',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'roles.viewAny',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'kernel-admin',
            'guard_name' => $guardName,
        ]);

        $adminRole->syncPermissions($permissions);

        $viewerRole = Role::firstOrCreate([
            'name' => 'kernel-viewer',
            'guard_name' => $guardName,
        ]);

        $viewerRole->syncPermissions([
            'panel.access',
            'api-keys.viewAny',
            'api-keys.view',
            'packages.viewAny',
            'packages.view',
            'mcp-entries.viewAny',
            'mcp-entries.view',
            'webhook-subscriptions.viewAny',
            'webhook-subscriptions.view',
            'webhook-deliveries.viewAny',
            'webhook-deliveries.view',
            'users.viewAny',
            'users.view',
            'roles.viewAny',
            'roles.view',
        ]);
    }
}
