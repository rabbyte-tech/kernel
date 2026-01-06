<?php

namespace App\Models;

use App\Traits\HasPublicUlid;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use RabbyteTech\Contracts\Auth\HasPermissions as HasPermissionContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasPermissionContract
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasPublicUlid, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasPermissionTo('panel.access');
    }
}
