<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string generateImage($schedule)
 */
class Schedule extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'schedule';
    }
}
