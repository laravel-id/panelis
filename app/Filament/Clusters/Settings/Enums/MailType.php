<?php

namespace App\Filament\Clusters\Settings\Enums;

use Composer\InstalledVersions;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum MailType: string implements HasDescription, HasLabel
{
    case Log = 'log';

    case Sendmail = 'sendmail';

    case SMTP = 'smtp';

    case Mailgun = 'mailgun';

    case Postmark = 'postmark';

    case Resend = 'resend';

    case SES = 'ses';

    public function getLabel(): string
    {
        return __(sprintf('setting.mail.%s.driver', $this->value));
    }

    public function getDescription(): ?string
    {
        return __(sprintf('setting.mail.%s.description', $this->value));
    }

    public function installed(): bool
    {
        $symfonyHttpInstalled = InstalledVersions::isInstalled('symfony/http-client');

        return match ($this) {
            self::Mailgun => InstalledVersions::isInstalled('symfony/mailgun-mailer') && $symfonyHttpInstalled,
            self::Postmark => InstalledVersions::isInstalled('symfony/postmark-mailer') && $symfonyHttpInstalled,
            self::SES => InstalledVersions::isInstalled('aws/aws-sdk-php'),
            self::Resend => InstalledVersions::isInstalled('resend/resend-laravel'),
            default => true,
        };
    }
}
