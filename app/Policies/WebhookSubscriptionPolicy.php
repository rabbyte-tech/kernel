<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebhookSubscription;

class WebhookSubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('webhook-subscriptions.viewAny');
    }

    public function view(User $user, WebhookSubscription $webhookSubscription): bool
    {
        return $user->hasPermissionTo('webhook-subscriptions.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('webhook-subscriptions.create');
    }

    public function update(User $user, WebhookSubscription $webhookSubscription): bool
    {
        return $user->hasPermissionTo('webhook-subscriptions.update');
    }

    public function delete(User $user, WebhookSubscription $webhookSubscription): bool
    {
        return $user->hasPermissionTo('webhook-subscriptions.delete');
    }
}
