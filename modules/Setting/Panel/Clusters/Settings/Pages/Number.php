<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\NumberFormat;
use Modules\Setting\Panel\Clusters\Settings\Enums\NumberPermission;
use Modules\Setting\Panel\Clusters\Settings\HasUpdateableForm;
use Modules\Setting\Panel\Clusters\Settings\UpdateSettingPage;

class Number extends UpdateSettingPage implements HasSchemas, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 50;

    public array $app;

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.number.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.number.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(NumberPermission::Browse);
    }

    public function updatePermission(): BackedEnum
    {
        return NumberPermission::Edit;
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
            Section::make(__('setting::setting.number.label'))
                ->disabled(! user_can(NumberPermission::Edit))
                ->description(__('setting::setting.number.section_description'))
                ->schema([
                    TextInput::make('app.currency_symbol')
                        ->label(__('setting::setting.number.currency_symbol'))
                        ->live()
                        ->minValue(1)
                        ->maxValue(10),

                    Toggle::make('app.number_symbol_suffix')
                        ->label(__('setting::setting.number.currency_symbol_as_suffix'))
                        ->helperText(__('setting::setting.number.helper_currency_symbol_as_suffix'))
                        ->live()
                        ->disabled(fn (Get $get): bool => empty($get('app.currency_symbol')))
                        ->nullable(),

                    Radio::make('app.number_format')
                        ->label(__('setting::setting.number.format'))
                        ->required()
                        ->live()
                        ->options(NumberFormat::class),

                    TextEntry::make('sample_display')
                        ->label(__('setting::setting.number.sample_display'))
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
}
