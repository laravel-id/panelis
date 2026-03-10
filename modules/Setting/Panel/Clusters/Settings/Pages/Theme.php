<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\ThemePermission;
use Modules\Setting\Panel\Clusters\Settings\HasUpdateableForm;
use Modules\Setting\Panel\Clusters\Settings\UpdateSettingPage;

class Theme extends UpdateSettingPage implements HasSchemas, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.theme.navigation');
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.theme.label');
    }

    public static function canAccess(): bool
    {
        return user_can(ThemePermission::Browse);
    }

    public static function updatePermission(): BackedEnum
    {
        return ThemePermission::Edit;
    }

    public array $colors = ['primary', 'gray', 'success', 'info', 'warning', 'danger'];

    public array $color;

    public function mount(): void
    {
        $this->form->fill([
            'color' => collect($this->colors)
                ->mapWithKeys(function (string $color): array {
                    return [$color => config(sprintf('color.%s', $color))];
                })
                ->toArray(),

            'isButtonDisabled' => user_cannot(ThemePermission::Edit),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $colorsInput = [];
        foreach ($this->colors as $color) {
            $colorsInput[] = ColorPicker::make(sprintf('color.%s', $color))
                ->label(__(sprintf('setting::setting.theme.color_%s', $color)))
                ->nullable()
                ->hexColor();
        }

        return $schema->schema([
            Section::make(__('setting::setting.theme.label'))
                ->description(__('setting::setting.theme.section_description'))
                ->schema($colorsInput),
        ]);
    }

    public function afterUpdated(array $forms): void
    {
        $this->js('window.location.reload()');
    }
}
