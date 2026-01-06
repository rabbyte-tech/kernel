<?php

return [
    'model' => 'Webhook Subscription',
    'model_plural' => 'Webhook Subscriptions',
    'sections' => [
        'details' => 'Details',
    ],
    'fields' => [
        'status' => 'Status',
        'name' => 'Name',
        'url' => 'URL',
        'event' => 'Event',
        'secret' => 'Secret',
        'is_active' => 'Active',
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
];
