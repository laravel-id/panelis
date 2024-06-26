<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static null|string getImage($schedule)
 * @method static null|string generateImage($schedule)
 */
class Schedule extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'schedule';
    }
}
