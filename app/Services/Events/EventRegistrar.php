<?php

namespace App\Services\Events;

use App\Services\Webhooks\PublishEventToWebhooks;
use Illuminate\Support\Facades\Event;

readonly class EventRegistrar
{
    public function __construct(
        private EventRegistry $eventRegistry
    ) {}

    public function register(): void
    {
        foreach ($this->eventRegistry->bindings() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($eventName, $listener);
            }
        }

        foreach (array_keys($this->eventRegistry->events()) as $eventName) {
            Event::listen($eventName, PublishEventToRedis::class);
            Event::listen($eventName, PublishEventToWebhooks::class);
        }
    }
}
