<?php

return [
    'max_attempts' => (int) env('WEBHOOK_MAX_ATTEMPTS', 5),
    'backoff_minutes' => array_map(
        'intval',
        array_filter(
            explode(',', (string) env('WEBHOOK_BACKOFF_MINUTES', '1,5,15,60,120'))
        )
    ),
];
