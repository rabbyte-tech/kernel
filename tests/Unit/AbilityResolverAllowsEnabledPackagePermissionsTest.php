<?php

it('allows permissions from enabled packages for active api key', function () {
    $permissionRegistry = $this->createMock(\App\Services\Packages\PackagePermissionRegistry::class);
    $permissionRegistry->method('enabledPermissions')->willReturn(['test.permission']);
    $permissionRegistry->method('allPackagePermissions')->willReturn(['test.permission']);

    $permission = \Spatie\Permission\Models\Permission::create([
        'name' => 'test.permission',
        'guard_name' => config('auth.defaults.guard'),
    ]);

    $role = \Spatie\Permission\Models\Role::create([
        'name' => 'test-role',
        'guard_name' => config('auth.defaults.guard'),
    ]);
    $role->givePermissionTo($permission);

    $apiKey = \App\Models\ApiKey::factory()->create(['is_active' => true]);
    $apiKey->roles()->attach($role);

    $resolver = new \App\Services\Authorization\AbilityResolver($permissionRegistry);

    $result = $resolver->allows($apiKey, 'test.permission');

    expect($result)->toBeTrue();
});
