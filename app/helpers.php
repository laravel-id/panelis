<?php

if (! function_exists('get_timezone')) {
    function get_timezone(): string
    {
        return config('app.datetime_timezone', config('app.timezone'));
    }
}
