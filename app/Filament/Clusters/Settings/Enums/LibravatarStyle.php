<?php

namespace App\Filament\Clusters\Settings\Enums;

use Filament\Support\Contracts\HasLabel;

enum LibravatarStyle: string implements HasLabel
{
    case Identicon = 'identicon';

    case MysteryMan = 'mmng';

    case Monster = 'monsterid';

    case Retro = 'retro';

    case RetroAdventure = 'pagan';

    case Roboter = 'robohash';

    case Wavatar = 'wavatar';

    public function getLabel(): string
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
