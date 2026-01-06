<?php

namespace App\Models;

use App\Enums\McpPrimitiveType;
use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class McpEntry extends Model
{
    use HasPublicUlid;

    protected $fillable = [
        'package_id',
        'type',
        'name',
        'class',
        'permission',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'type' => McpPrimitiveType::class,
            'is_enabled' => 'bool',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
