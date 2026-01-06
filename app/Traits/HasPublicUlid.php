<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

trait HasPublicUlid
{
    use HasUlids;

    public function uniqueIds(): array
    {
        return ['public_id'];
    }
}
