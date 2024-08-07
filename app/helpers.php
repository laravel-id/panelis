<?php

if (! function_exists('get_timezone')) {
    function get_timezone(): string
    {
        return config('app.datetime_timezone', config('app.timezone'));
    }
}

if (! function_exists('get_datetime_format')) {
    function get_datetime_format(): string
    {
        return config('app.datetime_format', 'Y-m-d H:i');
    }
}
