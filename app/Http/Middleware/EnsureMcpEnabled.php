<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureMcpEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('mcp.enabled', true)) {
            Log::warning('MCP request blocked by config.', [
                'path' => $request->path(),
            ]);

            return response()->json(['message' => 'MCP is disabled'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        Log::info('MCP request received.', [
            'path' => $request->path(),
        ]);

        return $next($request);
    }
}
