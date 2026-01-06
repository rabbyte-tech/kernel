<?php

it('builds envelope with required fields', function () {
    $eventRegistry = $this->createMock(\App\Services\Events\EventRegistry::class);
    $eventRegistry->method('definition')
        ->willReturn([
            'package' => 'test-package',
            'version' => '1.0.0',
        ]);

    $dispatcher = new \App\Services\Events\EventDispatcher($eventRegistry);

    $envelope = $dispatcher->dispatch('test.event', ['key' => 'value'], [
        'actor' => 'system',
    ]);

    expect($envelope)
        ->toBeArray()
        ->toHaveKeys(['event', 'id', 'occurred_at', 'actor', 'source', 'data', 'meta'])
        ->event->toBe('test.event')
        ->id->toBeString()
        ->occurred_at->toBeString()
        ->actor->toBeArray()
        ->source->toBeArray()
        ->data->toBeArray()
        ->meta->toBeArray();
});
