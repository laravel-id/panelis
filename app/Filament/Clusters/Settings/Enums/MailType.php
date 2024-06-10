<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;

enum MailType: string implements HasOption
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
                return [$case->value => $case->label()];
            })
            ->toArray();
    }

    public static function descriptions(): array
    {
        return collect(MailType::cases())
            ->mapWithKeys(function (MailType $case): array {
                return [$case->value => __(sprintf('setting.mail_description_%s', $case->value))];
            })
            ->toArray();
    }

    public function label(): string
    {
        return __(sprintf('setting.mail_type_%s', $this->value));
    }
}
