<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class Datetime extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Settings::class;

    public function getTitle(): string|Htmlable
    {
        return __('setting.datetime');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_datetime');
    }

    public array $app;

    public function mount()
    {
        $this->form->fill([
            'app.datetime_timezone' => config('app.datetime_timezone', config('app.timezone')),
            'app.datetime_format' => config('app.datetime_format', 'Y m d H:i'),
        ]);
    }

    public function form(Form $form): Form
    {
        $timezones = collect(\DateTimeZone::listIdentifiers());

        return $form->schema([
            Section::make(__('setting.datetime'))
                ->description(__('setting.datetime_section_description'))
                ->schema([
                    // do not override existing config from Laravel: "app.timezone"
                    // default timezone should be in UTC, but display timezone is interchangeable
                    Select::make('app.datetime_timezone')
                        ->options(array_combine($timezones->toArray(), $timezones->toArray()))
                        ->required()
                        ->searchable()
                        ->live()
                        ->label(__('setting.datetime_timezone')),

                    TextInput::make('app.datetime_format')
                        ->label(__('setting.datetime_format'))
                        ->hint(function (): Htmlable {
                            $html = __('setting.datetime_format_sample', [
                                'link' => 'https://www.php.net/manual/en/datetime.format.php',
                            ]);

                            return new HtmlString($html);
                        })
                        ->hintColor('primary')
                        ->live()
                        ->required(),

                    Placeholder::make('datetime_sample')
                        ->label(__('setting.datetime_sample'))
                        ->content(function (Get $get): string {
                            return now($get('app.datetime_timezone'))
                                ->translatedFormat($get('app.datetime_format'));
                        }),
                ]),
        ]);
    }

    public function update(): void
    {
        $this->validate();

        event(new SettingUpdated);

        foreach (Arr::dot($this->form->getState()) as $key => $value) {
            Setting::updateOrCreate(compact('key'), ['value' => $value ?? '']);
        }

        Notification::make('datetime_updated')
            ->title(__('setting.datetime_updated'))
            ->success()
            ->send();
    }
}
