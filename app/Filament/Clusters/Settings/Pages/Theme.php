<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class Theme extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_theme');
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.theme');
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

    public function form(Form $form): Form
    {
        $colorsInput = [];
        foreach ($this->colors as $color) {
            $colorsInput[] = ColorPicker::make(sprintf('color.%s', $color))
                ->label(__(sprintf('setting.color_%s', $color)));
        }

        return $form->schema([
            Section::make(__('setting.theme'))
                ->description(__('setting.theme_section_description'))
                ->schema($colorsInput),
        ]);
    }

    public function update(): void
    {
        try {
            $this->validate();

            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            event(new SettingUpdated);

            Notification::make('theme_updated')
                ->title('setting.theme_updated')
                ->success()
                ->send();

            $this->js('window.location.reload()');
        } catch (\Exception $e) {
            Log::error($e);

            Notification::make('theme_not_updated')
                ->title(__('setting.theme_not_updated'))
                ->warning()
                ->send();
        }
    }
}
