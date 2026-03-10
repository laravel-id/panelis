<?php

namespace Tests\Unit\Config;

it('defines correct environment for test', function (): void {
    expect(env('APP_ENV'))->toBe('testing');
});
