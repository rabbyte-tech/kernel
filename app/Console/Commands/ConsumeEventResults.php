<?php

namespace App\Console\Commands;

use App\Services\Events\EventRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use JsonException;
use Throwable;

class ConsumeEventResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume event results from Redis streams';

    /**
     * Execute the console command.
     */
    public function handle(EventRegistry $eventRegistry): int
    {
        $connection = config('events.streams.connection', 'stream');
        $resultPrefix = config('events.streams.result_prefix', 'events.result.');
        $group = config('events.streams.result_group', 'kernel-results');
        $blockMs = (int) config('events.streams.result_block_ms', 5000);
        $batchSize = (int) config('events.streams.result_batch_size', 100);
        $claimIdleMs = (int) config('events.streams.result_claim_idle_ms', 60000);
        $claimBatchSize = (int) config('events.streams.result_claim_batch_size', 100);
        $claimIntervalSeconds = (int) config('events.streams.result_claim_interval_seconds', 30);
        $cleanupIdleMs = (int) config('events.streams.result_consumer_cleanup_idle_ms', $claimIdleMs);
        $cleanupIntervalSeconds = (int) config('events.streams.result_consumer_cleanup_interval_seconds', $claimIntervalSeconds);
        $consumer = 'kernel-'.gethostname().'-'.getmypid();
        $lastClaimedAt = 0;
        $lastCleanupAt = 0;

        while (true) {
            try {
                $streams = $this->resultStreams($eventRegistry, $resultPrefix);

                if ($streams === []) {
                    usleep(250000);

                    continue;
                }

                $this->ensureGroupsExist($connection, $streams, $group);

                $messages = $this->readGroupMessages(
                    $connection,
                    $streams,
                    $group,
                    $consumer,
                    $batchSize,
                    $blockMs
                );
                $this->processMessages($connection, $resultPrefix, $group, $messages);

                $now = time();
                if ($now - $lastClaimedAt >= $claimIntervalSeconds) {
                    $this->claimPendingMessages(
                        $connection,
                        $streams,
                        $resultPrefix,
                        $group,
                        $consumer,
                        $claimIdleMs,
                        $claimBatchSize
                    );

                    $lastClaimedAt = $now;
                }

                if ($now - $lastCleanupAt >= $cleanupIntervalSeconds) {
                    $this->cleanupIdleConsumers(
                        $connection,
                        $streams,
                        $group,
                        $consumer,
                        $cleanupIdleMs
                    );

                    $lastCleanupAt = $now;
                }
            } catch (Throwable $exception) {
                Log::warning('Redis subscription dropped. Retrying.', [
                    'error' => $exception->getMessage(),
                ]);

                try {
                    Redis::connection($connection)->disconnect();
                } catch (Throwable $disconnectException) {
                    Log::warning('Failed to disconnect Redis after subscription drop.', [
                        'error' => $disconnectException->getMessage(),
                    ]);
                }

                try {
                    Redis::purge($connection);
                } catch (Throwable $purgeException) {
                    Log::warning('Failed to purge Redis connection after subscription drop.', [
                        'error' => $purgeException->getMessage(),
                    ]);
                }

            }
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, array<int, array<int, mixed>>>  $messages
     */
    private function processMessages(
        string $connection,
        string $resultPrefix,
        string $group,
        array $messages
    ): void {
        if ($messages === []) {
            return;
        }

        foreach ($messages as $stream => $entries) {
            foreach ($entries as $entryId => $fields) {
                if (! is_string($entryId) || ! is_array($fields)) {
                    Log::warning('Invalid message entry ID.', [
                        'entry_id' => $entryId,
                    ]);

                    continue;
                }

                $this->handleEntry($connection, $resultPrefix, $group, $stream, $entryId, $fields);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function handleEntry(
        string $connection,
        string $resultPrefix,
        string $group,
        string $stream,
        string $entryId,
        array $fields
    ): void {
        $payload = $this->decodePayload($fields);
        $maxlen = (int) config('events.streams.result_maxlen', 5000);

        try {
            if ($payload === null) {
                Log::warning('Event result payload missing or invalid; skipping dispatch.');

                return;
            }

            $eventName = str_starts_with($stream, $resultPrefix)
                ? substr($stream, strlen($resultPrefix))
                : $stream;

            if ($eventName === '') {
                Log::warning('Event result payload missing event name in stream.');

                return;
            }

            Event::dispatch('events.result.'.$eventName, [$payload]);
        } finally {
            Redis::connection($connection)->xack($stream, $group, [$entryId]);
            Redis::connection($connection)->xtrim($stream, $maxlen, true);
        }
    }

    /**
     * @param  array<int, string>  $streams
     */
    private function claimPendingMessages(
        string $connection,
        array $streams,
        string $resultPrefix,
        string $group,
        string $consumer,
        int $claimIdleMs,
        int $claimBatchSize
    ): void {
        foreach ($streams as $stream) {
            try {
                $result = Redis::connection($connection)->xautoclaim(
                    $stream,
                    $group,
                    $consumer,
                    $claimIdleMs,
                    '0-0',
                    $claimBatchSize
                );
            } catch (Throwable $exception) {
                Log::warning('Failed to claim pending messages.', [
                    'stream' => $stream,
                    'group' => $group,
                    'error' => $exception->getMessage(),
                ]);

                continue;
            }

            if (! is_array($result)) {
                continue;
            }

            $claimed = $result[1] ?? [];

            if (! is_array($claimed) || $claimed === []) {
                continue;
            }

            foreach ($claimed as $entry) {
                if (is_array($entry) && isset($entry[0], $entry[1])) {
                    $entryId = $entry[0];
                    $fields = $entry[1];

                    if (is_string($entryId) && is_array($fields)) {
                        $this->handleEntry($connection, $resultPrefix, $group, $stream, $entryId, $fields);
                    }

                    continue;
                }

                if (is_array($entry)) {
                    foreach ($entry as $entryId => $fields) {
                        if (is_string($entryId) && is_array($fields)) {
                            $this->handleEntry($connection, $resultPrefix, $group, $stream, $entryId, $fields);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array<string>
     */
    private function resultStreams(EventRegistry $eventRegistry, string $prefix): array
    {
        $streams = [];

        foreach (array_keys($eventRegistry->events()) as $eventName) {
            $streams[] = $prefix.$eventName;
        }

        return $streams;
    }

    /**
     * @param  array<int, string>  $streams
     */
    private function ensureGroupsExist(string $connection, array $streams, string $group): void
    {
        foreach ($streams as $stream) {
            try {
                Redis::connection($connection)->command('xgroup', ['CREATE', $stream, $group, '0', 'MKSTREAM']);
            } catch (Throwable $exception) {
                if (! str_contains($exception->getMessage(), 'BUSYGROUP')) {
                    Log::warning('Failed to create Redis stream group.', [
                        'stream' => $stream,
                        'group' => $group,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * @param  array<int, string>  $streams
     * @return array<string, array<int, array<int, mixed>>>
     */
    private function readGroupMessages(
        string $connection,
        array $streams,
        string $group,
        string $consumer,
        int $batchSize,
        int $blockMs
    ): array {
        $streamMap = [];

        foreach ($streams as $stream) {
            $streamMap[$stream] = '>';
        }

        $messages = Redis::connection($connection)->xreadgroup(
            $group,
            $consumer,
            $streamMap,
            $batchSize,
            $blockMs
        );

        if (! is_array($messages)) {
            return [];
        }

        return $messages;
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>|null
     */
    private function decodePayload(array $fields): ?array
    {
        $payload = $fields['payload'] ?? null;

        if (! is_string($payload)) {
            Log::warning('Event result payload missing payload field.');

            return null;
        }

        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            Log::warning('Failed to decode event result payload.', [
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        if (! is_array($decoded)) {
            Log::warning('Event result payload is not an array.');

            return null;
        }

        return $decoded;
    }

    /**
     * @param  array<int, string>  $streams
     */
    private function cleanupIdleConsumers(
        string $connection,
        array $streams,
        string $group,
        string $activeConsumer,
        int $idleMs
    ): void {
        foreach ($streams as $stream) {
            try {
                $consumers = Redis::connection($connection)->command('xinfo', ['CONSUMERS', $stream, $group]);
            } catch (Throwable $exception) {
                Log::warning('Failed to fetch Redis consumers.', [
                    'stream' => $stream,
                    'group' => $group,
                    'error' => $exception->getMessage(),
                ]);

                continue;
            }

            if (! is_array($consumers)) {
                Log::warning('Redis consumers response is not an array.', [
                    'stream' => $stream,
                    'group' => $group,
                    'response' => $consumers,
                ]);

                continue;
            }

            foreach ($consumers as $consumerInfo) {
                $info = $this->normalizeConsumerInfo($consumerInfo);

                if ($info === null) {
                    continue;
                }

                if ($info['name'] === $activeConsumer) {
                    continue;
                }

                if ($info['idle'] < $idleMs) {
                    continue;
                }

                try {
                    Redis::connection($connection)->command('xgroup', ['DELCONSUMER', $stream, $group, $info['name']]);
                } catch (Throwable $exception) {
                    Log::warning('Failed to delete idle Redis consumer.', [
                        'stream' => $stream,
                        'group' => $group,
                        'consumer' => $info['name'],
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * @param  array<int, mixed>  $consumerInfo
     * @return array{name: string, idle: int}|null
     */
    private function normalizeConsumerInfo(array $consumerInfo): ?array
    {
        if (isset($consumerInfo['name'], $consumerInfo['idle'])) {
            $name = $consumerInfo['name'];
            $idle = $consumerInfo['idle'];

            if (is_string($name) && is_int($idle)) {
                return [
                    'name' => $name,
                    'idle' => $idle,
                ];
            }
        }

        $name = null;
        $idle = null;

        foreach ($consumerInfo as $index => $value) {
            if ($value === 'name') {
                $name = $consumerInfo[$index + 1] ?? null;
            }

            if ($value === 'idle') {
                $idle = $consumerInfo[$index + 1] ?? null;
            }
        }

        if (! is_string($name) || ! is_int($idle)) {
            return null;
        }

        return [
            'name' => $name,
            'idle' => $idle,
        ];
    }
}
