<?php

namespace App\Jobs;

use App\Enums\WebhookDeliveryStatus;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DeliverWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $deliveryId
    ) {}

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $delivery = WebhookDelivery::query()
            ->with('webhookSubscription')
            ->find($this->deliveryId);

        if (! $delivery) {
            return;
        }

        if ($delivery->status !== WebhookDeliveryStatus::Pending) {
            return;
        }

        $subscription = $delivery->webhookSubscription;

        if (! $subscription || ! $subscription->is_active) {
            $delivery->forceFill([
                'status' => WebhookDeliveryStatus::Failed,
                'last_error' => 'Subscription is inactive.',
                'next_attempt_at' => null,
            ])->save();

            return;
        }

        $payload = is_array($delivery->payload) ? $delivery->payload : [];
        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

        if ($payloadJson === false) {
            $payloadJson = '{}';
        }

        $headers = [
            'Content-Type' => 'application/json',
            'X-Rabbyte-Event' => $delivery->event_name,
            'X-Rabbyte-Delivery' => $delivery->public_id,
        ];

        if (is_string($subscription->secret) && $subscription->secret !== '') {
            $signature = hash_hmac('sha256', $payloadJson, $subscription->secret);
            $headers['X-Rabbyte-Signature'] = 'sha256='.$signature;
        }

        $attempt = $delivery->attempt + 1;
        $response = Http::timeout(10)
            ->withHeaders($headers)
            ->withBody($payloadJson, 'application/json')
            ->send('POST', $subscription->url);

        if ($response->successful()) {
            $delivery->forceFill([
                'attempt' => $attempt,
                'status' => WebhookDeliveryStatus::Succeeded,
                'last_error' => null,
                'next_attempt_at' => null,
            ])->save();

            return;
        }

        $maxAttempts = (int) config('webhooks.max_attempts', 5);
        $backoff = config('webhooks.backoff_minutes', [1, 5, 15, 60, 120]);
        $backoffMinutes = (int) (is_array($backoff) ? ($backoff[$attempt - 1] ?? end($backoff)) : 5);
        $lastError = Str::limit(
            trim((string) $response->status().' '.$response->body()),
            1000
        );

        if ($attempt >= $maxAttempts) {
            $delivery->forceFill([
                'attempt' => $attempt,
                'status' => WebhookDeliveryStatus::Failed,
                'last_error' => $lastError,
                'next_attempt_at' => null,
            ])->save();

            return;
        }

        $delaySeconds = max($backoffMinutes, 1) * 60;

        $delivery->forceFill([
            'attempt' => $attempt,
            'status' => WebhookDeliveryStatus::Pending,
            'last_error' => $lastError,
            'next_attempt_at' => now()->addSeconds($delaySeconds),
        ])->save();

        $this->release($delaySeconds);
    }
}
