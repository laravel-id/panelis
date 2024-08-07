<?php

namespace App\Services\OAuth;

use App\Services\OAuth\Vendors\Dropbox;
use App\Services\OAuth\Vendors\GoogleDrive;

class OAuthFactory
{
    const GoogleDrive = 'google_drive';

    const Dropbox = 'dropbox';

    public static function make(): OAuth
    {
        return match (config('oauth.provider')) {
            self::GoogleDrive => new GoogleDrive,
            default => new Dropbox,
        };
    }
}
