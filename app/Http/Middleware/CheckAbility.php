<?php

namespace App\Http\Middleware;

use App\Services\Authorization\AbilityResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class CheckAbility
{
    public function __construct(
        private AbilityResolver $abilityResolver
    ) {}

    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $actor = $request->user();

        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $hasAbility = false;
        foreach ($abilities as $ability) {
            if ($this->abilityResolver->allows($actor, $ability)) {
                $hasAbility = true;
                break;
            }
        }

        if (! $hasAbility) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
