<?php

namespace App\Actions\Packages;

use App\Enums\PackageStatus;
use App\Models\Package;
use RabbyteTech\Support\Actions\DomainAction;

class SetPackageStatus extends DomainAction
{
    protected function ability(): ?string
    {
        return 'packages.update';
    }

    public function execute(Package $package, PackageStatus $status): bool
    {
        $this->authorize();

        if ($package->status === $status) {
            return false;
        }

        $package->status = $status;
        $package->save();

        return true;
    }
}
