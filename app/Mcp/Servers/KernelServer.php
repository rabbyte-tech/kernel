<?php

namespace App\Mcp\Servers;

use App\Services\Mcp\McpRegistry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as HttpRequest;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\ServerContext;

class KernelServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Kernel Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        You are connected to the Rabbyte Kernel MCP server.

        Use only the tools, resources, and prompts exposed by this server. They are filtered for the current actor and their permissions.

        Kernel scope:
        - This server provides platform wiring only (packages, permissions, events, MCP entries, webhooks).
        - Do not assume any product/domain features exist unless a tool or resource explicitly exposes them.
        - Prefer actions that are safe and auditable; do not attempt mutations unless the tool explicitly supports it.

        When you need data, prefer reading via provided resources or tools. If a request cannot be fulfilled with the available primitives, explain what is missing (e.g. tool or permission).
    MARKDOWN;

    public function createContext(): ServerContext
    {
        $registry = app(McpRegistry::class);
        $actor = HttpRequest::user();
        $actorType = $actor ? $actor->getMorphClass() : null;
        $actorId = $actor?->getKey();

        Log::info('MCP context resolved.', [
            'actor_type' => $actorType,
            'actor_id' => $actorId,
        ]);

        $tools = $registry->toolsFor($actor);
        $resources = $registry->resourcesFor($actor);
        $prompts = $registry->promptsFor($actor);

        Log::info('MCP primitives resolved.', [
            'tools' => count($tools),
            'resources' => count($resources),
            'prompts' => count($prompts),
        ]);

        return new ServerContext(
            supportedProtocolVersions: $this->supportedProtocolVersion,
            serverCapabilities: $this->capabilities,
            serverName: $this->name,
            serverVersion: $this->version,
            instructions: $this->instructions,
            maxPaginationLength: $this->maxPaginationLength,
            defaultPaginationLength: $this->defaultPaginationLength,
            tools: $tools,
            resources: $resources,
            prompts: $prompts,
        );
    }
}
