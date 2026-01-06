<?php

it('enforces permission and denies access when actor lacks ability', function () {
    $abilityResolver = $this->createMock(\App\Services\Authorization\AbilityResolver::class);
    $abilityResolver->method('allows')->willReturn(false);

    $package = \App\Models\Package::factory()->create(['status' => \App\Enums\PackageStatus::Disabled]);
    $originalStatus = $package->status;

    $action = new \App\Actions\Packages\SetPackageStatus($abilityResolver);

    expect(fn () => $action->execute($package, \App\Enums\PackageStatus::Enabled))
        ->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);

    expect($package->refresh()->status)
        ->toBe($originalStatus);
});
