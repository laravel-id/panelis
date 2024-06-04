<?php

namespace App\Models\Enums;

enum MailType: string
{
    case Log = 'log';

    case Sendmail = 'sendmail';

    case SMTP = 'smtp';

    case Mailgun = 'mailgun';

    case Postmark = 'postmark';

    case SES = 'ses';

    public static function options(): array
    {
        return collect(MailType::cases())
            ->mapWithKeys(function (MailType $case): array {
                return [$case->value => $case->getLabel()];
            })
            ->toArray();
    }

    public static function descriptions(): array
    {
        return collect(MailType::cases())
            ->mapWithKeys(function (MailType $case): array {
                return [$case->value => __(sprintf('setting.mail_%s_description', $case->value))];
            })
            ->toArray();
    }

    public function getLabel(): string
    {
        return __(sprintf('setting.mail_type_%s', $this->value));
    }
}
