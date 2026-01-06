<?php

namespace App\Models;

use App\Traits\HasPublicUlid;
use Database\Factories\ApiKeyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class ApiKey extends Model
{
    /** @use HasFactory<ApiKeyFactory> */
    use HasApiTokens, HasFactory, HasPublicUlid;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('permission.models.role'), 'api_key_roles');
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function (Builder $query) use ($permission): void {
                $query->where('name', $permission);
            })
            ->exists();
    }
}
