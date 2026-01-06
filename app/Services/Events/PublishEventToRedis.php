<?php

namespace App\Services\Events;

readonly class PublishEventToRedis
{
    public function __construct(
        private EventPublisher $eventPublisher
    ) {}

    /**
     * @param  array<string, mixed>  $event
     */
    public function __invoke(array $event): void
    {
        $this->eventPublisher->publish($event);
    }
}
