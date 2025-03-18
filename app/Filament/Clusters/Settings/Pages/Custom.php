<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\CustomSettingPermission;
use App\Models\Setting;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Custom extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 9;

    public array $custom;

    public function getTitle(): string|Htmlable
    {
        return __('setting.custom');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_custom');
    }

    public static function canAccess(): bool
    {
        return user_can(CustomSettingPermission::Browse);
    }

    public function mount(): void
    {

        $this->form->fill([
            'custom' => Setting::query()
                ->where('is_custom', true)
                ->get()
                ->toArray(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(user_cannot(CustomSettingPermission::Browse))
            ->schema([
                Section::make(__('setting.custom'))
                    ->description(__('setting.custom_section_description'))
                    ->schema([
                        Repeater::make('custom')
                            ->label(__('setting.custom_key_value'))
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                            ->collapsed()
                            ->schema([
                                TextInput::make('key')
                                    ->label(__('setting.key'))
                                    ->required(),

                                Textarea::make('value')
                                    ->label(__('setting.value')),
                            ])
                            ->deleteAction(function (Action $action): void {
                                $action->requiresConfirmation()
                                    ->before(function (array $arguments, array $state) {
                                        $setting = $state[$arguments['item']];

                                        if (! empty($setting['key'])) {
                                            Setting::query()
                                                ->where([
                                                    'is_custom' => true,
                                                    'key' => $setting['key'],
                                                ])
                                                ->delete();
                                        }
                                    });
                            }),
                    ]),
            ]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(CustomSettingPermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            foreach ($this->form->getState()['custom'] as $setting) {
                // you aren't allowed to override defined setting in panel
                $exists = Setting::query()
                    ->where([
                        'key' => $setting['key'],
                        'is_custom' => false,
                    ])
                    ->exists();

                if ($exists) {
                    continue;
                }

                Setting::set($setting['key'], $setting['value'], isCustom: true);
            }

            event(new SettingUpdated);

            Notification::make('custom_setting_updated')
                ->success()
                ->title(__('setting.custom_setting_updated'))
                ->send();
        } catch (Exception $e) {
            Log::error($e);

            Notification::make('custom_setting_not_updated')
                ->danger()
                ->title(__('setting.custom_setting_not_updated'))
                ->send();
        }
    }
}
