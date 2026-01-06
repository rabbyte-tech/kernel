<?php

return [
    'model' => 'Package',
    'model_plural' => 'Packages',
    'sections' => [
        'info' => 'Info',
        'meta' => 'Meta',
    ],
    'fields' => [
        'name' => 'Name',
        'version' => 'Version',
        'manifest_hash' => 'Manifest Hash',
        'status' => 'Status',
        'created_at' => 'Created At',
    ],
    'actions' => [
        'enable' => 'Enable',
        'disable' => 'Disable',
    ],
    'status' => [
        'installed' => 'Installed',
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
        'unknown' => 'Unknown',
    ],
];
