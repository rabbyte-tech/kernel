<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebhookDelivery;

class WebhookDeliveryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('webhook-deliveries.viewAny');
    }

    public function view(User $user, WebhookDelivery $webhookDelivery): bool
    {
        return $user->hasPermissionTo('webhook-deliveries.view');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, WebhookDelivery $webhookDelivery): bool
    {
        return false;
    }

    public function delete(User $user, WebhookDelivery $webhookDelivery): bool
    {
        return false;
    }
}
