<?php

namespace App\Enums;

enum Disk: string
{
    case Public = 'public';

    case Private = 'private';

    case Local = 'local';

    case S3 = 's3';

    case Dropbox = 'dropbox';
}
