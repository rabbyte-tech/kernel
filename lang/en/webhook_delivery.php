<?php

return [
    'model' => 'Webhook Delivery',
    'model_plural' => 'Webhook Deliveries',
    'sections' => [
        'details' => 'Details',
        'metadata' => 'Metadata',
        'payload' => 'Payload',
        'error' => 'Error',
    ],
    'fields' => [
        'event' => 'Event',
        'attempt' => 'Attempt',
        'next_attempt' => 'Next Attempt',
        'subscription' => 'Subscription',
        'request_payload' => 'Request Payload',
        'last_error' => 'Last Error',
        'status' => 'Status',
        'created_at' => 'Created At',
    ],
];
