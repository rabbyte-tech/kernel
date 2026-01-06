<?php

namespace App\Services\Webhooks;

use App\Enums\WebhookDeliveryStatus;
use App\Jobs\DeliverWebhook;
use App\Models\WebhookDelivery;
use App\Models\WebhookSubscription;
use Illuminate\Support\Str;

class WebhookDispatcher
{
    /**
     * @param  array<string, mixed>  $envelope
     */
    public function dispatch(array $envelope): void
    {
        $eventName = $envelope['event'] ?? null;

        if (! is_string($eventName) || $eventName === '') {
            return;
        }

        $eventId = $envelope['id'] ?? null;

        if (! is_string($eventId) || $eventId === '') {
            $eventId = (string) Str::uuid();
        }

        $subscriptions = WebhookSubscription::query()
            ->where('is_active', true)
            ->where('event', $eventName)
            ->get();

        foreach ($subscriptions as $subscription) {
            $delivery = WebhookDelivery::query()->create([
                'webhook_subscription_id' => $subscription->id,
                'event_name' => $eventName,
                'event_id' => $eventId,
                'payload' => $envelope,
                'attempt' => 0,
                'status' => WebhookDeliveryStatus::Pending,
            ]);

            DeliverWebhook::dispatch($delivery->id)
                ->onConnection('redis')
                ->onQueue('events');
        }
    }
}
