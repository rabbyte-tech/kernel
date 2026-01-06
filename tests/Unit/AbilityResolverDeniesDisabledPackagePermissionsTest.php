<?php

it('denies permissions from disabled packages', function () {
    $permissionRegistry = $this->createMock(\App\Services\Packages\PackagePermissionRegistry::class);
    $permissionRegistry->method('enabledPermissions')->willReturn([]);
    $permissionRegistry->method('allPackagePermissions')->willReturn(['test.permission']);

    $permission = \Spatie\Permission\Models\Permission::create([
        'name' => 'test.permission',
        'guard_name' => config('auth.defaults.guard'),
    ]);

    $resolver = new \App\Services\Authorization\AbilityResolver($permissionRegistry);

    $result = $resolver->allows(null, 'test.permission');

    expect($result)->toBeFalse();
});
