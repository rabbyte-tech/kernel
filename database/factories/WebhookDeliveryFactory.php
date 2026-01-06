<?php

namespace Database\Factories;

use App\Enums\WebhookDeliveryStatus;
use App\Models\WebhookDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookDeliveryFactory extends Factory
{
    protected $model = WebhookDelivery::class;

    public function definition(): array
    {
        return [
            'webhook_subscription_id' => null,
            'event_name' => fake()->word,
            'event_id' => fake()->uuid,
            'payload' => [],
            'attempt' => fake()->numberBetween(0, 3),
            'status' => fake()->randomElement(WebhookDeliveryStatus::class),
            'last_error' => fake()->optional()->sentence,
            'next_attempt_at' => fake()->optional()->dateTime(),
        ];
    }
}
