<?php

return [
    'streams' => [
        'connection' => env('EVENTS_STREAM_CONNECTION', 'stream'),
        'event_prefix' => env('EVENTS_STREAM_EVENT_PREFIX', 'events.'),
        'result_prefix' => env('EVENTS_STREAM_RESULT_PREFIX', 'events.result.'),
        'event_maxlen' => env('EVENTS_STREAM_EVENT_MAXLEN', 10000),
        'result_maxlen' => env('EVENTS_STREAM_RESULT_MAXLEN', 5000),
        'result_group' => env('EVENTS_STREAM_RESULT_GROUP', 'kernel-results'),
        'result_block_ms' => env('EVENTS_STREAM_RESULT_BLOCK_MS', 5000),
        'result_batch_size' => env('EVENTS_STREAM_RESULT_BATCH_SIZE', 100),
        'result_claim_idle_ms' => env('EVENTS_STREAM_RESULT_CLAIM_IDLE_MS', 60000),
        'result_claim_batch_size' => env('EVENTS_STREAM_RESULT_CLAIM_BATCH_SIZE', 100),
        'result_claim_interval_seconds' => env('EVENTS_STREAM_RESULT_CLAIM_INTERVAL_SECONDS', 30),
        'result_consumer_cleanup_idle_ms' => env('EVENTS_STREAM_RESULT_CONSUMER_CLEANUP_IDLE_MS', 60000),
        'result_consumer_cleanup_interval_seconds' => env('EVENTS_STREAM_RESULT_CONSUMER_CLEANUP_INTERVAL_SECONDS', 30),
    ],
];
