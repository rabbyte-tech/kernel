<?php

namespace App\Services\Mcp;

use App\Enums\McpPrimitiveType;
use App\Enums\PackageStatus;
use App\Models\ApiKey;
use App\Models\McpEntry;
use App\Services\Authorization\AbilityResolver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class McpRegistry
{
    public function __construct(
        private readonly AbilityResolver $abilityResolver,
    ) {}

    /**
     * @return array<int, class-string>
     */
    public function toolsFor(?object $actor): array
    {
        return $this->classesFor($actor, McpPrimitiveType::Tool);
    }

    /**
     * @return array<int, class-string>
     */
    public function resourcesFor(?object $actor): array
    {
        return $this->classesFor($actor, McpPrimitiveType::Resource);
    }

    /**
     * @return array<int, class-string>
     */
    public function promptsFor(?object $actor): array
    {
        return $this->classesFor($actor, McpPrimitiveType::Prompt);
    }

    /**
     * @return array<int, class-string>
     */
    private function classesFor(?object $actor, McpPrimitiveType $type): array
    {
        if (! config('mcp.enabled', true)) {
            Log::info('MCP registry disabled by config.', ['type' => $type->value]);

            return [];
        }

        if (! $actor instanceof ApiKey) {
            Log::info('MCP registry blocked for non-API key actor.', [
                'type' => $type->value,
                'actor_type' => $actor?->getMorphClass(),
            ]);

            return [];
        }

        $entries = $this->entriesFor($type);

        if ($entries->isEmpty()) {
            Log::info('MCP registry has no entries.', ['type' => $type->value]);

            return [];
        }

        return $entries
            ->filter(fn (McpEntry $entry): bool => $this->abilityResolver->allows($actor, $entry->permission))
            ->map(fn (McpEntry $entry): string => $entry->class)
            ->filter(fn (string $class): bool => class_exists($class))
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, McpEntry>
     */
    private function entriesFor(McpPrimitiveType $type): Collection
    {
        if (! Schema::hasTable('mcp_entries') || ! Schema::hasTable('packages')) {
            return new Collection;
        }

        return McpEntry::query()
            ->select('mcp_entries.*')
            ->join('packages', 'packages.id', '=', 'mcp_entries.package_id')
            ->where('packages.status', PackageStatus::Enabled->value)
            ->where('mcp_entries.is_enabled', true)
            ->where('mcp_entries.type', $type->value)
            ->get();
    }
}
