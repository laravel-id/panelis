<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;

enum LibravatarStyle: string implements HasOption
{
    case Identicon = 'identicon';

    case MysteryMan = 'mmng';

    case Monster = 'monsterid';

    case Retro = 'retro';

    case RetroAdventure = 'pagan';

    case Roboter = 'robohash';

    case Wavatar = 'wavatar';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(function (self $case): array {
                return [$case->value => $case->label()];
            })
            ->sort()
            ->toArray();
    }

    public function label(): string
    {
        return match ($this->value) {
            'robohash' => 'Roboter',
            'pagan' => 'Retro adventure style',
            'wavatar' => 'Wavatar style',
            'monsterid' => 'Monster style',
            'identicon' => 'Identicon style',
            'mmng' => 'Mystery man NextGen',
            default => 'Retro style',
        };
    }
}
