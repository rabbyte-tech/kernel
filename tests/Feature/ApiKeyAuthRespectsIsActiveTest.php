<?php

it('rejects inactive api key', function () {
    $apiKey = \App\Models\ApiKey::factory()->create(['is_active' => false]);
    $token = $apiKey->createToken('test-token');

    $request = \Illuminate\Http\Request::create('/api/test', 'GET');
    $request->headers->set('Authorization', 'Bearer '.$token->plainTextToken);
    $request->setUserResolver(fn () => $apiKey);

    $middleware = new \App\Http\Middleware\EnsureApiKeyIsActive;

    $response = $middleware->handle($request, fn () => response()->json(['success' => true]));

    expect($response->status())->toBe(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toBe('API key is not active');
});

it('accepts active api key', function () {
    $apiKey = \App\Models\ApiKey::factory()->create(['is_active' => true]);
    $token = $apiKey->createToken('test-token');

    $request = \Illuminate\Http\Request::create('/api/test', 'GET');
    $request->headers->set('Authorization', 'Bearer '.$token->plainTextToken);
    $request->setUserResolver(fn () => $apiKey);

    $middleware = new \App\Http\Middleware\EnsureApiKeyIsActive;

    $response = $middleware->handle($request, fn () => response()->json(['success' => true]));

    expect($response->status())->toBe(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    $data = json_decode($response->getContent(), true);
    expect($data['success'])->toBeTrue();
});
