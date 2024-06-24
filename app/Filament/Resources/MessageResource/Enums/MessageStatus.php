<?php

namespace App\Filament\Resources\MessageResource\Enums;

use App\Models\Enums\HasOption;

enum MessageStatus: string implements HasOption
{
    case Unread = 'unread';

    case Read = 'read';

    case Replied = 'replied';

    case Resolved = 'resolved';

    case Spam = 'spam';

    public static function options(): array
    {
        return collect(MessageStatus::cases())
            ->mapWithKeys(function (MessageStatus $case): array {
                return [$case->value => $case->label()];
            })
            ->toArray();
    }

    public function label(): string
    {
        return __(sprintf('message.status_%s', $this->value));
    }

    public function color(): string
    {
        return match ($this->value) {
            'spam' => 'warning',
            default => 'info',
        };
    }
}
