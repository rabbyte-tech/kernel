<?php

namespace App\Services\Webhooks;

readonly class PublishEventToWebhooks
{
    public function __construct(
        private WebhookDispatcher $webhookDispatcher
    ) {}

    /**
     * @param  array<string, mixed>  $event
     */
    public function __invoke(array $event): void
    {
        $this->webhookDispatcher->dispatch($event);
    }
}
