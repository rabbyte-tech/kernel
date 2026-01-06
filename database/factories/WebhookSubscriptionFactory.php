<?php

namespace Database\Factories;

use App\Models\WebhookSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookSubscriptionFactory extends Factory
{
    protected $model = WebhookSubscription::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'url' => fake()->url,
            'event' => fake()->word().'.'.fake()->word(),
            'secret' => fake()->optional()->password,
            'is_active' => true,
        ];
    }
}
