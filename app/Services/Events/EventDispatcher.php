<?php

namespace App\Services\Events;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RabbyteTech\Contracts\Events\EventDispatcher as EventDispatcherContract;

readonly class EventDispatcher implements EventDispatcherContract
{
    private const SCHEMA_VERSION = '1';

    public function __construct(
        private EventRegistry $eventRegistry
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{
     *   actor?: Model|array{type?: string, id?: string, name?: string}|string,
     *   actor_type?: string,
     *   actor_id?: string,
     *   actor_name?: string,
     *   request_id?: string,
     *   correlation_id?: string,
     *   causation_id?: string,
     *   schema_version?: string,
     *   meta?: array<string, mixed>
     * }  $context
     * @return array<string, mixed>
     */
    public function dispatch(string $eventName, array $payload = [], array $context = []): array
    {
        $definition = $this->eventRegistry->definition($eventName);

        if ($definition === null) {
            throw new InvalidArgumentException("Event '{$eventName}' is not registered.");
        }

        $meta = is_array($context['meta'] ?? null) ? $context['meta'] : [];

        if (isset($context['correlation_id'])) {
            $meta['correlation_id'] = (string) $context['correlation_id'];
        }

        if (isset($context['causation_id'])) {
            $meta['causation_id'] = (string) $context['causation_id'];
        }

        if (isset($context['request_id'])) {
            $meta['request_id'] = (string) $context['request_id'];
        }

        $meta['schema_version'] = (string) ($context['schema_version'] ?? ($meta['schema_version'] ?? self::SCHEMA_VERSION));

        $envelope = [
            'event' => $eventName,
            'id' => (string) Str::uuid(),
            'occurred_at' => now()->toIso8601String(),
            'actor' => $this->normalizeActor($context),
            'source' => [
                'package' => $definition['package'],
                'version' => $definition['version'],
            ],
            'data' => $payload,
            'meta' => $meta,
        ];

        Event::dispatch($eventName, [$envelope]);

        return $envelope;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function normalizeActor(array $context): array
    {
        $actor = $context['actor'] ?? null;
        $type = $context['actor_type'] ?? null;
        $id = $context['actor_id'] ?? null;
        $name = $context['actor_name'] ?? null;

        if ($actor instanceof Model) {
            $type ??= $this->resolveActorType($actor);
            $id ??= (string) $actor->getKey();
            $nameAttribute = $actor->getAttribute('name');

            if (is_string($nameAttribute) && $nameAttribute !== '') {
                $name ??= $nameAttribute;
            }
        }

        if (is_string($actor) && $actor !== '' && ! is_string($type)) {
            $type = $actor;
        }

        if (! is_string($type) || $type === '') {
            $type = 'system';
        }

        $actorData = ['type' => $type];

        if (is_string($id) && $id !== '') {
            $actorData['id'] = $id;
        }

        if (is_string($name) && $name !== '') {
            $actorData['name'] = $name;
        }

        return $actorData;
    }

    private function resolveActorType(Model $actor): string
    {
        if ($actor instanceof User) {
            return 'user';
        }

        if ($actor instanceof ApiKey) {
            return 'api_key';
        }

        return 'worker';
    }
}
