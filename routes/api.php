<?php

use App\Services\Packages\PackageRegistry;

foreach (app(PackageRegistry::class)->enabledApiRouteFiles() as $routesFile) {
    require $routesFile;
}
