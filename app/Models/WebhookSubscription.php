<?php

namespace App\Models;

use App\Traits\HasPublicUlid;
use Database\Factories\WebhookSubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookSubscription extends Model
{
    /** @use HasFactory<WebhookSubscriptionFactory> */
    use HasFactory, HasPublicUlid;

    protected $fillable = [
        'name',
        'url',
        'event',
        'secret',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'secret' => 'encrypted',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }
}
