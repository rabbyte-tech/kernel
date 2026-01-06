<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof ApiKey) {
            return response()->json(['message' => 'Invalid authentication type'], Response::HTTP_FORBIDDEN);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'API key is not active'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
