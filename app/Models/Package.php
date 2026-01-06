<?php

namespace App\Models;

use App\Enums\PackageStatus;
use App\Traits\HasPublicUlid;
use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory, HasPublicUlid;

    protected $fillable = [
        'name',
        'version',
        'status',
        'manifest_hash',
    ];

    protected function casts(): array
    {
        return [
            'status' => PackageStatus::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
