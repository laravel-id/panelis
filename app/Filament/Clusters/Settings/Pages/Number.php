<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\NumberFormat;
use App\Filament\Clusters\Settings\Enums\NumberPermission;
use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Number extends Page
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Calculator;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 5;

    public array $app;

    public function getTitle(): string|Htmlable
    {
        return __('setting.number.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.number.navigation');
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
                'number_format' => config('app.number_format', NumberFormat::Plain->value),
                'number_symbol_suffix' => config('app.number_symbol_suffix', false),
            ],

            'isButtonDisabled' => user_cannot(NumberPermission::Edit),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('setting.number.label'))
                ->disabled(! user_can(NumberPermission::Edit))
                ->description(__('setting.number.section_description'))
                ->schema([
                    TextInput::make('app.currency_symbol')
                        ->label(__('setting.number.currency_symbol'))
                        ->live()
                        ->minValue(1)
                        ->maxValue(10),

                    Toggle::make('app.number_symbol_suffix')
                        ->label(__('setting.number.currency_symbol_as_suffix'))
                        ->helperText(__('setting.number.helper_currency_symbol_as_suffix'))
                        ->live()
                        ->disabled(fn (Get $get): bool => empty($get('app.currency_symbol')))
                        ->nullable(),

                    Radio::make('app.number_format')
                        ->label(__('setting.number.format'))
                        ->required()
                        ->live()
                        ->options(NumberFormat::class),

                    TextEntry::make('sample_display')
                        ->label(__('setting.number.sample_display'))
                        ->state(function (Get $get): ?string {
                            $format = $get('app.number_format') ?? NumberFormat::Plain;

                            return \Illuminate\Support\Number::money(
                                10_234_567.12,
                                $format->value,
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

            Notification::make('number.setting_updated')
                ->title(__('filament-actions::edit.single.notifications.saved.title'))
                ->success()
                ->send();
        } catch (Exception) {
            Notification::make('number.setting_not_updated')
                ->title(__('setting.number.not_updated'))
                ->warning()
                ->send();
        }
    }
}
