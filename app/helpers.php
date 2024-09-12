<?php

use Illuminate\Support\Facades\Log;

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

if (! function_exists('get_date_format')) {
    function get_date_format(): string
    {
        $key = 'app.date_format';
        if (! config()->has($key)) {
            Log::warning(sprintf('Key config %s does not exists.', $key));
        }

        return config($key, default: 'Y-m-d');
    }
}

if (! function_exists('get_time_format')) {
    function get_time_format(): string
    {
        $key = 'app.time_format';
        if (! config()->has($key)) {
            Log::warning(sprintf('Key config %s does not exists.', $key));
        }

        return config($key, default: 'H:i');
    }
}

if (! function_exists('get_color_theme')) {
    function get_color_theme(?string $selected = null): string
    {
        return $selected ?? config('color.theme', 'zinc');
    }
}

if (! function_exists('set_locale')) {
    function set_locale(string $locale): void
    {
        $locales = config('app.locales', [config('app.locale')]);
        if ($locale != app()->getLocale() && in_array($locale, $locales)) {
            app()->setLocale($locale);
        }
    }
}
