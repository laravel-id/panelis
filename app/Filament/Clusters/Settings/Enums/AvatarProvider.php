<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\User;
use BackedEnum;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Auth\Authenticatable;

enum AvatarProvider: string implements HasLabel
{
    case UIAvatars = 'ui-avatars';

    case Gravatar = 'gravatar';

    case Libravatar = 'libravatar';

    case Unavatar = 'Unavatar';

    private function getGravatarImageUrl(User $user): ?string
    {
        return sprintf('https://gravatar.com/avatar/%s', hash('sha256', $user->email));
    }

    private function getLibravatarImageUrl(User $user, ?string $style = null): ?string
    {
        $hash = hash('sha256', $user->email);
        $style = $style ?? config('user.avatar_libravatar_style', 'retro');

        return sprintf('https://www.libravatar.org/avatar/%s?s=80&forcedefault=y&default=%s', $hash, $style);
    }

    private function getUIAvatarsImageUrl(User $user): ?string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($user->name);
    }

    private function getUnavatarImageUrl(User $user): ?string
    {
        return 'https://unavatar.io/'.$user->email;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Gravatar => 'Gravatar (gravatar.com)',
            self::Libravatar => 'Libravatar (libravatar.org)',
            self::Unavatar => 'Unavatar.io',
            default => 'UI Avatars (ui-avatars.com)',
        };
    }

    public function getImageUrl(User|Authenticatable $user, ?BackedEnum $style = null): ?string
    {
        return match ($this) {
            self::Gravatar => $this->getGravatarImageUrl($user),
            self::Libravatar => $this->getLibravatarImageUrl($user, $style->value ?? null),
            self::Unavatar => $this->getUnavatarImageurl($user),
            default => $this->getUIAvatarsImageUrl($user),
        };
    }
}
