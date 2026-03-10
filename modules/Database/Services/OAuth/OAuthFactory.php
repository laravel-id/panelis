<?php

namespace Modules\Database\Services\OAuth;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use Modules\Database\Services\OAuth\Vendors\Dropbox;
use Modules\Database\Services\OAuth\Vendors\GoogleDrive;

/**
 * @mixin OAuth
 */
class OAuthFactory extends Manager
{
    const string GoogleDrive = 'google_drive';

    const string Dropbox = 'dropbox';

    protected $drivers = [
        self::Dropbox,
        self::GoogleDrive,
    ];

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function getDefaultDriver(): string
    {
        return self::Dropbox;
    }

    protected function createDropboxDriver(): OAuth
    {
        return new Dropbox;
    }

    protected function createGoogleDriveDriver(): OAuth
    {
        return new GoogleDrive;
    }
}
