<?php

namespace App\Providers;

use App\Models\ApiKey;
use App\Models\McpEntry;
use App\Models\Package;
use App\Models\User;
use App\Models\WebhookDelivery;
use App\Models\WebhookSubscription;
use App\Policies\ApiKeyPolicy;
use App\Policies\McpEntryPolicy;
use App\Policies\PackagePolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\WebhookDeliveryPolicy;
use App\Policies\WebhookSubscriptionPolicy;
use App\Services\Authorization\AbilityResolver as PlatformAbilityResolver;
use App\Services\Events\EventDispatcher as PlatformEventDispatcher;
use App\Services\Events\EventRegistrar;
use App\Services\Packages\PackageRegistry as PlatformPackageRegistry;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use RabbyteTech\Contracts\Authorization\AbilityResolver;
use RabbyteTech\Contracts\Events\EventDispatcher;
use RabbyteTech\Contracts\Packages\PackageRegistry;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PackageRegistry::class, PlatformPackageRegistry::class);
        $this->app->bind(EventDispatcher::class, PlatformEventDispatcher::class);
        $this->app->bind(AbilityResolver::class, PlatformAbilityResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ApiKey::class, ApiKeyPolicy::class);
        Gate::policy(McpEntry::class, McpEntryPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
        Gate::policy(WebhookSubscription::class, WebhookSubscriptionPolicy::class);
        Gate::policy(WebhookDelivery::class, WebhookDeliveryPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        app(EventRegistrar::class)->register();

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'cs']);
        });
    }
}
