<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\DatetimePermission;
use App\Models\Setting;
use BackedEnum;
use DateTimeZone;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

class Datetime extends Page
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::CalendarDays;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Settings::class;

    public array $app;

    public function getTitle(): string|Htmlable
    {
        return __('setting.datetime.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.datetime.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(DatetimePermission::Browse);
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
            Section::make(__('setting.datetime.label'))
                ->disabled(user_cannot(DatetimePermission::Edit))
                ->description(__('setting.datetime.section_description'))
                ->schema([
                    // do not override existing config from Laravel: "app.timezone"
                    // default timezone should be in UTC, but display timezone is interchangeable
                    Select::make('app.datetime_timezone')
                        ->options(array_combine($timezones->toArray(), $timezones->toArray()))
                        ->required()
                        ->searchable()
                        ->in($timezones->toArray())
                        ->live()
                        ->label(__('setting.datetime.timezone')),

                    TextInput::make('app.datetime_format')
                        ->label(__('setting.datetime.format'))
                        ->hint(function (): Htmlable {
                            return str(__('setting.datetime.format_sample'))
                                ->inlineMarkdown()
                                ->toHtmlString();
                        })
                        ->hintColor('primary')
                        ->live()
                        ->required(),

                    TextEntry::make('datetime.sample')
                        ->label(__('setting.datetime.sample'))
                        ->state(function (Get $get): string {
                            return now($get('app.datetime_timezone'))
                                ->translatedFormat($get('app.datetime_format'));
                        }),
                ]),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(DatetimePermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        foreach (Arr::dot($this->form->getState()) as $key => $value) {
            Setting::updateOrCreate(compact('key'), ['value' => $value ?? '']);
        }

        event(new SettingUpdated);

        Notification::make('datetime.updated')
            ->title(__('filament-actions::edit.single.notifications.saved.title'))
            ->success()
            ->send();
    }
}
