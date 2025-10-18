<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\ThemePermission;
use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Theme extends Page implements HasForms
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::PaintBrush;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('setting.theme.navigation');
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.theme.label');
    }

    public static function canAccess(): bool
    {
        return user_can(ThemePermission::Browse);
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
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $colorsInput = [];
        foreach ($this->colors as $color) {
            $colorsInput[] = ColorPicker::make(sprintf('color.%s', $color))
                ->label(__(sprintf('setting.theme.color_%s', $color)))
                ->nullable()
                ->hexColor();
        }

        return $schema->schema([
            Section::make(__('setting.theme.label'))
                ->description(__('setting.theme.section_description'))
                ->schema($colorsInput),
        ]);
    }

    public function update(): void
    {
        abort_unless(user_can(ThemePermission::Edit), Response::HTTP_FORBIDDEN);

        try {
            $this->validate();

            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            event(new SettingUpdated);

            Notification::make('theme_updated')
                ->title(__('filament-actions::edit.single.notifications.saved.title'))
                ->success()
                ->send();

            $this->js('window.location.reload()');
        } catch (Exception $e) {
            Log::error($e);

            Notification::make('theme_not_updated')
                ->title(__('setting.theme_not_updated'))
                ->warning()
                ->send();
        }
    }
}
