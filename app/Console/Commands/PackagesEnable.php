<?php

namespace App\Console\Commands;

use App\Actions\Packages\SetPackageStatus;
use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Console\Command;

class PackagesEnable extends Command
{
    protected $signature = 'packages:enable {name : Package name from the manifest}';

    protected $description = 'Enable a package by name.';

    public function handle(SetPackageStatus $setPackageStatus): int
    {
        $name = (string) $this->argument('name');

        $package = Package::query()->where('name', $name)->first();

        if (! $package) {
            $this->error("Package not found: {$name}");

            return self::FAILURE;
        }

        $changed = $setPackageStatus->execute($package, PackageStatus::Enabled);

        if ($changed) {
            $this->info("Package enabled: {$package->name}");
        } else {
            $this->line("Package already enabled: {$package->name}");
        }

        return self::SUCCESS;
    }
}
