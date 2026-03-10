<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use DateTimeZone;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\DatetimePermission;
use Modules\Setting\Panel\Clusters\Settings\HasUpdateableForm;
use Modules\Setting\Panel\Clusters\Settings\UpdateSettingPage;

class Datetime extends UpdateSettingPage implements HasSchemas, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?int $navigationSort = 40;

    protected static ?string $cluster = Settings::class;

    public array $app;

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.datetime.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.datetime.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(DatetimePermission::Browse);
    }

    public function updatePermission(): BackedEnum
    {
        return DatetimePermission::Edit;
    }

    public function mount()
    {
        $this->form->fill([
            'app.datetime_timezone' => config('app.datetime_timezone', config('app.timezone')),
            'app.datetime_format' => config('app.datetime_format', 'Y-m-d H:i'),

            'isButtonDisabled' => user_cannot(DatetimePermission::Edit),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $timezones = collect(DateTimeZone::listIdentifiers());

        return $schema->components([
            Section::make(__('setting::setting.datetime.label'))
                ->disabled(user_cannot(DatetimePermission::Edit))
                ->description(__('setting::setting.datetime.section_description'))
                ->schema([
                    // do not override existing config from Laravel: "app.timezone"
                    // default timezone should be in UTC, but display timezone is interchangeable
                    Select::make('app.datetime_timezone')
                        ->options(array_combine($timezones->toArray(), $timezones->toArray()))
                        ->required()
                        ->searchable()
                        ->in($timezones->toArray())
                        ->live()
                        ->label(__('setting::setting.datetime.timezone')),

                    TextInput::make('app.datetime_format')
                        ->label(__('setting::setting.datetime.format'))
                        ->hint(function (): Htmlable {
                            return str(__('setting::setting.datetime.format_sample'))
                                ->inlineMarkdown()
                                ->toHtmlString();
                        })
                        ->hintColor('primary')
                        ->live()
                        ->required(),

                    TextEntry::make('datetime.sample')
                        ->label(__('setting::setting.datetime.sample'))
                        ->state(function (Get $get): string {
                            return now($get('app.datetime_timezone'))
                                ->translatedFormat($get('app.datetime_format'));
                        }),
                ]),
        ]);
    }
}
