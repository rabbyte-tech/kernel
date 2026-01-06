<?php

namespace App\Console\Commands;

use App\Actions\Packages\SetPackageStatus;
use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Console\Command;

class PackagesDisable extends Command
{
    protected $signature = 'packages:disable {name : Package name from the manifest}';

    protected $description = 'Disable a package by name.';

    public function handle(SetPackageStatus $setPackageStatus): int
    {
        $name = (string) $this->argument('name');

        $package = Package::query()->where('name', $name)->first();

        if (! $package) {
            $this->error("Package not found: {$name}");

            return self::FAILURE;
        }

        $changed = $setPackageStatus->execute($package, PackageStatus::Disabled);

        if ($changed) {
            $this->info("Package disabled: {$package->name}");
        } else {
            $this->line("Package already disabled: {$package->name}");
        }

        return self::SUCCESS;
    }
}
