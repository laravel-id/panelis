<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;
use App\Models\User;

enum AvatarProvider: string implements HasOption
{
    case UIAvatars = 'ui-avatars';

    case Gravatar = 'gravatar';

    private function getGravatarImageUrl(User $user): ?string
    {
        return sprintf('https://gravatar.com/avatar/%s', hash('sha256', $user->email));
    }

    private function getUIAvatarsImageUrl(): ?string
    {
        return null;
    }

    public static function options(): array
    {
        return collect(AvatarProvider::cases())
            ->mapWithKeys(function (AvatarProvider $case): array {
                return [$case->value => $case->label()];
            })
            ->sort()
            ->toArray();
    }

    public function label(): string
    {
        return match ($this->value) {
            'gravatar' => 'Gravatar (gravatar.com)',
            default => 'UI Avatars (ui-avatars.com)',
        };
    }

    public function getImageUrl(User $user): ?string
    {
        return match ($this->value) {
            'gravatar' => $this->getGravatarImageUrl($user),
            default => $this->getUIAvatarsImageUrl($user),
        };
    }
}
