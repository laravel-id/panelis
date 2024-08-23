<?php

namespace App\Services\OAuth;

use App\Services\OAuth\Vendors\Dropbox;
use App\Services\OAuth\Vendors\GoogleDrive;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

/**
 * @mixin OAuth
 */
class OAuthFactory extends Manager
{
    const GoogleDrive = 'google_drive';

    const Dropbox = 'dropbox';

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
