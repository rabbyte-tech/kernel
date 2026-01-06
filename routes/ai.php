<?php

use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', \App\Mcp\Servers\KernelServer::class)
    ->middleware(['auth:sanctum', 'api_key_active', 'mcp_enabled']);
