<?php

namespace App\Filament\Clusters\Settings\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum MailType: string implements HasDescription, HasLabel
{
    case Log = 'log';

    case Sendmail = 'sendmail';

    case SMTP = 'smtp';

    case Mailgun = 'mailgun';

    case Postmark = 'postmark';

    case SES = 'ses';

    public function getLabel(): string
    {
        return __(sprintf('setting.mail.%s_driver', $this->value));
    }

    public function getDescription(): ?string
    {
        return __(sprintf('setting.mail.%s_description', $this->value));
    }
}
