<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $public_id
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\ApiKeyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUpdatedAt($value)
 */
	class ApiKey extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $public_id
 * @property int $package_id
 * @property \App\Enums\McpPrimitiveType $type
 * @property string $name
 * @property string $class
 * @property string $permission
 * @property bool $is_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|McpEntry whereUpdatedAt($value)
 */
	class McpEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $public_id
 * @property string $name
 * @property string $version
 * @property \App\Enums\PackageStatus $status
 * @property string $manifest_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PackageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereManifestHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Package whereVersion($value)
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $public_id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser, \RabbyteTech\Contracts\Auth\HasPermissions {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $public_id
 * @property int $webhook_subscription_id
 * @property string $event_name
 * @property string $event_id
 * @property array<array-key, mixed> $payload
 * @property int $attempt
 * @property \App\Enums\WebhookDeliveryStatus $status
 * @property string|null $last_error
 * @property \Illuminate\Support\Carbon|null $next_attempt_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\WebhookSubscription $webhookSubscription
 * @method static \Database\Factories\WebhookDeliveryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereAttempt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereLastError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereNextAttemptAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery whereWebhookSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookDelivery withoutTrashed()
 */
	class WebhookDelivery extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $public_id
 * @property string $name
 * @property string $url
 * @property string $event
 * @property string|null $secret
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WebhookDelivery> $deliveries
 * @property-read int|null $deliveries_count
 * @method static \Database\Factories\WebhookSubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookSubscription whereUrl($value)
 */
	class WebhookSubscription extends \Eloquent {}
}

