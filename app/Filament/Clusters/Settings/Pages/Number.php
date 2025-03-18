<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\NumberFormat;
use App\Filament\Clusters\Settings\Enums\NumberPermission;
use App\Models\Setting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Number extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 5;

    public array $app;

    public function getTitle(): string|Htmlable
    {
        return __('setting.number');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_number');
    }

    public static function canAccess(): bool
    {
        return user_can(NumberPermission::Browse);
    }

    public function mount(): void
    {

        $this->form->fill([
            'app' => [
                'currency_symbol' => config('app.currency_symbol'),
                'number_format' => config('app.number_format', '0 . ,'),
                'number_symbol_suffix' => config('app.number_symbol_suffix', false),
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('setting.number'))
                ->disabled(user_can(NumberPermission::Edit))
                ->description(__('setting.number_section_description'))
                ->schema([
                    TextInput::make('app.currency_symbol')
                        ->label(__('setting.number_currency_symbol'))
                        ->live()
                        ->minValue(1)
                        ->maxValue(10),

                    Toggle::make('app.number_symbol_suffix')
                        ->label(__('setting.number_currency_symbol_as_suffix'))
                        ->helperText(__('setting.number_helper_currency_symbol_as_suffix'))
                        ->live()
                        ->disabled(fn (Get $get): bool => empty($get('app.currency_symbol')))
                        ->nullable(),

                    Radio::make('app.number_format')
                        ->label(__('setting.number_format'))
                        ->required()
                        ->live()
                        ->options(NumberFormat::options())
                        ->enum(NumberFormat::class),

                    Placeholder::make('sample_display')
                        ->label(__('setting.number_sample_display'))
                        ->content(function (Get $get): ?string {
                            $format = $get('app.number_format');

                            // at some point, Laravel/Filament trim space at suffix
                            // we need to restore it with some condition
                            if (count(explode(' ', $format)) === 2) {
                                $format = sprintf('%s ', $format);
                            }

                            return \Illuminate\Support\Number::money(
                                10_000.12,
                                $format,
                                $get('app.currency_symbol'),
                                $get('app.number_symbol_suffix'),
                            );
                        }),
                ]),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(NumberPermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            event(new SettingUpdated);

            Notification::make('number_setting_updated')
                ->title(__('setting.number_updated'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make('number_setting_not_updated')
                ->title(__('setting.number_not_updated'))
                ->warning()
                ->send();
        }
    }
}
