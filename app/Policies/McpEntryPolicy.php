<?php

namespace App\Policies;

use App\Models\McpEntry;
use App\Models\User;

class McpEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('mcp-entries.viewAny');
    }

    public function view(User $user, McpEntry $mcpEntry): bool
    {
        return $user->hasPermissionTo('mcp-entries.view');
    }

    public function update(User $user, McpEntry $mcpEntry): bool
    {
        return $user->hasPermissionTo('mcp-entries.update');
    }
}
