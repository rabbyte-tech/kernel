<?php

return [
    'model' => 'API Key',
    'model_plural' => 'API Keys',
    'sections' => [
        'details' => 'Details',
    ],
    'fields' => [
        'name' => 'Name',
        'roles' => 'Roles',
        'status' => 'Status',
        'last_used_at' => 'Last Used',
        'created_at' => 'Created At',
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
    'messages' => [
        'created' => 'API Key Created Successfully',
        'token_body' => 'Your API Token: :token',
    ],
];
