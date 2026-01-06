<?php

namespace App\Console\Commands;

use App\Actions\Packages\RegisterPackages;
use Illuminate\Console\Command;

class PackagesSync extends Command
{
    protected $signature = 'packages:sync';

    protected $description = 'Discover local packages and sync them into the database.';

    public function handle(RegisterPackages $registerPackages): int
    {
        $result = $registerPackages->execute();

        $this->info('Package sync completed.');
        $this->line("Registered: {$result['registered']}");
        $this->line("Updated: {$result['updated']}");
        $this->line("Disabled: {$result['disabled']}");

        return self::SUCCESS;
    }
}
