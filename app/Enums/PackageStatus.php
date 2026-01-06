<?php

namespace App\Enums;

enum PackageStatus: string
{
    case Installed = 'installed';
    case Enabled = 'enabled';
    case Disabled = 'disabled';
}
