<?php

return [
    'model' => 'MCP Entry',
    'model_plural' => 'MCP Entries',
    'sections' => [
        'info' => 'Info',
        'meta' => 'Meta',
    ],
    'fields' => [
        'package' => 'Package',
        'status' => 'Status',
        'class' => 'Class',
        'type' => 'Type',
        'name' => 'Name',
        'permission' => 'Permission',
        'updated_at' => 'Updated At',
    ],
    'actions' => [
        'enable' => 'Enable',
        'disable' => 'Disable',
    ],
    'status' => [
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
        'unknown' => 'Unknown',
    ],
];
