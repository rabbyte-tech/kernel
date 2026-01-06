<?php

namespace App\Services\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use JsonException;

class EventPublisher
{
    /**
     * @param  array<string, mixed>  $envelope
     */
    public function publish(array $envelope): void
    {
        $eventName = $envelope['event'] ?? null;

        if (! is_string($eventName) || $eventName === '') {
            Log::warning('Event envelope missing event name; cannot publish to Redis channel.');

            return;
        }

        $connection = config('events.streams.connection', 'stream');
        $prefix = config('events.streams.event_prefix', 'events.');
        $stream = $prefix.$eventName;
        $maxlen = (int) config('events.streams.event_maxlen', 10000);

        try {
            $payload = json_encode($envelope, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            Log::warning('Failed to encode event envelope for Redis.', [
                'error' => $exception->getMessage(),
            ]);

            return;
        }

        Redis::connection($connection)->xadd(
            $stream,
            '*',
            ['payload' => $payload],
            $maxlen,
            true
        );
    }
}
