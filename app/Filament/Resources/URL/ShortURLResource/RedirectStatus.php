<?php

namespace App\Filament\Resources\URL\ShortURLResource;

use App\Models\Enums\HasOption;

enum RedirectStatus: int implements HasOption
{
    case MovedPermanently = 301;

    case Found = 302;

    case SeeOther = 303;

    case TemporaryRedirect = 307;

    case PermanentRedirect = 308;

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public function label(): string
    {
        return __(sprintf('url.redirect_status_%s', $this->value));
    }
}
