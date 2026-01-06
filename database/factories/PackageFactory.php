<?php

namespace Database\Factories;

use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word,
            'version' => fake()->semver,
            'status' => fake()->randomElement(PackageStatus::class),
            'manifest_hash' => fake()->sha256,
        ];
    }
}
