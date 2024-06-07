<?php

use App\Filament\Clusters\Databases\Pages\DatabaseType;

it('defines correct environment for test', function (): void {
    expect(env('APP_ENV'))->toBe('testing');
});

it('uses sqlite as default database driver', function () {
    expect(env('DB_CONNECTION'))->toBe(DatabaseType::SQLite->value);
});
