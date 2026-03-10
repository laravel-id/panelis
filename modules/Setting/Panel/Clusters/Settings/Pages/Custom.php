<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\Setting\Events\SettingUpdated;
use Modules\Setting\Models\Setting;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\CustomSettingPermission;
use Modules\Setting\Panel\Clusters\Settings\HasUpdateableForm;
use Modules\Setting\Panel\Clusters\Settings\UpdateSettingPage;
use Symfony\Component\HttpFoundation\Response;

class Custom extends UpdateSettingPage implements HasSchemas, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 80;

    public array $custom;

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.custom.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.custom.navigation');
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

            'isButtonDisabled' => user_cannot(CustomSettingPermission::Edit),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(user_cannot(CustomSettingPermission::Browse))
            ->schema([
                Section::make(__('setting::setting.custom.label'))
                    ->description(__('setting::setting.custom.section_description'))
                    ->schema([
                        Repeater::make('custom')
                            ->hiddenLabel()
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                            ->collapsed()
                            ->orderColumn('key')
                            ->schema([
                                TextInput::make('key')
                                    ->label(__('setting::setting.key'))
                                    ->required(),

                                Textarea::make('value')
                                    ->label(__('setting::setting.value')),

                                Textarea::make('comment')
                                    ->label(__('setting::setting.custom.comment'))
                                    ->placeholder(__('setting::setting.custom.placeholder_comment')),
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

                Setting::set(
                    key: $setting['key'],
                    value: $setting['value'],
                    isCustom: true,
                    comment: $setting['comment'],
                );
            }

            event(new SettingUpdated);

            Notification::make()
                ->success()
                ->title(__('setting::setting.notifications.updated.title'))
                ->send();
        } catch (Exception $e) {
            Log::error($e);

            Notification::make()
                ->danger()
                ->title(__('setting::setting.custom.updated'))
                ->body($e->getMessage())
                ->send();
        }
    }
}
