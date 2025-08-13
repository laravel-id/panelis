<?php

use App\Filament\Clusters\Databases\Enums\DatabaseType;

it('defines correct environment for test', function (): void {
    expect(env('APP_ENV'))->toBe('testing');
});
