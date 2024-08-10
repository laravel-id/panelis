<?php

namespace App\Services\OAuth;

use Illuminate\Support\Manager;

/**
 * @mixin OAuth
 */
class OAuthFactory extends Manager
{
    const GoogleDrive = 'google_drive';

    const Dropbox = 'dropbox';

    public function getDefaultDriver(): string
    {
        return self::Dropbox;
    }
}
