<?php

namespace App\Filament\Clusters\Settings\Enums;

use Illuminate\Support\Str;

enum PicoTheme: string
{
    case RED = 'red';

    case Pink = 'pink';

    case Fuchia = 'fuchia';

    case Purple = 'purple';

    case Violet = 'violet';

    case Indigo = 'indigo';

    case Blue = 'blue';

    case Azure = 'azure';

    case Cyan = 'cyan';

    case Jade = 'jade';

    case Green = 'green';

    case Lime = 'lime';

    case Yellow = 'yellow';

    case Amber = 'amber';

    case Pumpkin = 'pumpkin';

    case Orange = 'orange';

    case Sand = 'sand';

    case Grey = 'grey';

    case Zinc = 'zinc';

    case Slate = 'slate';

    public static function options(): array
    {
        return collect(PicoTheme::cases())
            ->mapWithKeys(function (PicoTheme $case): array {
                return [$case->value => $case->htmlLabel()];
            })
            ->sort()
            ->toArray();
    }

    public function label(): string
    {
        return Str::ucfirst($this->value);
    }

    public function htmlLabel(): string
    {
        return sprintf('<span style="color: %s">%s</span>', $this->hexColor(), Str::ucfirst($this->value));
    }

    public function hexColor(): ?string
    {
        return match ($this->value) {
            'red' => '#c52f21',
            'pink' => '#d92662',
            'fuchia' => '#c1208b',
            'purple' => '#9236a4',
            'violet' => '#7540bf',
            'indigo' => '#524ed2',
            'blue' => '#2060df',
            'azure' => '#0172ad',
            'cyan' => '#047878',
            'jade' => '#007a50',
            'green' => '#398712',
            'lime' => '#a5d601',
            'yellow' => '#f2df0d',
            'amber' => '#ffbf00',
            'pumpkin' => '#ff9500',
            'orange' => '#d24317',
            'sand' => '#ccc6b4',
            'grey' => '#ababab',
            'zinc' => '#646b79',
            'slate' => '#525f7a',
            default => null,
        };
    }
}
