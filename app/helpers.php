<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

if (! function_exists('user_can')) {
    function user_can(BackedEnum $ability, ?Model $model = null, ?string $field = null): bool
    {
        $authorized = Auth::user()->can($ability->value);

        if (! empty($model)) {
            if (! empty($field)) {
                return $model->{$field} == Auth::id() && $authorized;
            }

            return $model->user_id == Auth::id() && $authorized;
        }

        return $authorized;
    }
}

if (! function_exists('user_cannot')) {
    function user_cannot(BackedEnum $ability, ?Model $model = null, ?string $field = null): bool
    {
        return ! user_can($ability, $model, $field);
    }
}

if (! function_exists('human_number')) {
    function human_number(int|float $number): string
    {
        if (empty(config('app.number_format'))) {
            Log::warning('Config app.number_format is not set. Using default: "0 . ,".');

            return number_format($number);
        }

        [$decimal, $thousand, $separator] = explode(' ', config('app.number_format'));

        return number_format($number, $decimal, $thousand, $separator);
    }
}

if (! function_exists('get_logo')) {
    function get_logo(): ?string
    {
        if (filled(config('app.logo'))) {
            return Storage::url(config('app.logo'));
        }

        return null;
    }
}

if (! function_exists('get_favicon')) {
    function get_favicon(): ?string
    {
        if (filled(config('app.favicon'))) {
            return Storage::url('app.favicon');
        }

        return null;
    }
}
