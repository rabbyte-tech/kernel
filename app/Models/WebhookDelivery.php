<?php

namespace App\Models;

use App\Enums\WebhookDeliveryStatus;
use App\Traits\HasPublicUlid;
use Database\Factories\WebhookDeliveryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookDelivery extends Model
{
    /** @use HasFactory<WebhookDeliveryFactory> */
    use HasFactory, HasPublicUlid, SoftDeletes;

    protected $fillable = [
        'webhook_subscription_id',
        'event_name',
        'event_id',
        'payload',
        'attempt',
        'status',
        'last_error',
        'next_attempt_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'status' => WebhookDeliveryStatus::class,
            'attempt' => 'integer',
            'next_attempt_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function webhookSubscription(): BelongsTo
    {
        return $this->belongsTo(WebhookSubscription::class);
    }
}
