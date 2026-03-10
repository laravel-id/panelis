<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\PanelPermission;
use Modules\Setting\Panel\Clusters\Settings\HasUpdateableForm;
use Modules\Setting\Panel\Clusters\Settings\Traits\AddUpdateButton;
use Modules\Setting\Panel\Clusters\Settings\UpdateSettingPage;

class Panel extends UpdateSettingPage implements HasSchemas, HasUpdateableForm
{
    use AddUpdateButton;
    use InteractsWithForms;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 11;

    public array $panelis;

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.panel.navigation');
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.panel.label');
    }

    public static function canAccess(): bool
    {
        return user_can(PanelPermission::Browse);
    }

    public static function updatePermission(): BackedEnum
    {
        return PanelPermission::Edit;
    }

    public function mount(): void
    {
        $this->form->fill([
            'panelis' => [
                'multitenant' => config('panelis.multitenant', false),
                'url' => config('panelis.url'),
                'path' => config('panelis.path'),
            ],

            'isButtonDisabled' => user_cannot(PanelPermission::Edit),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('setting::setting.panel.label'))
                    ->description(__('setting::setting.panel.section_description'))
                    ->schema([
                        Toggle::make('panelis.multitenant')
                            ->label(__('setting::setting.panel.enable_multitenant'))
                            ->hint(str(__('setting::setting.panel.multitenant_hint'))->inlineMarkdown()->toHtmlString())
                            ->helperText(__('setting::setting.panel.multitenant_helper')),

                        TextInput::make('panelis.url')
                            ->label(__('setting::setting.panel.url'))
                            ->nullable()
                            ->live(onBlur: true)
                            ->url(),

                        TextInput::make('panelis.path')
                            ->label(__('setting::setting.panel.path'))
                            ->prefix(function (Get $get): string {
                                if (! empty($get('panelis.url'))) {
                                    return rtrim($get('panelis.url'), '/').'/';
                                }

                                return config('app.url').'/';
                            })
                            ->nullable(),
                    ]),
            ]);
    }

    public function afterUpdated(array $forms): void
    {
        $path = $forms['panelis']['path'] ?? '';

        $this->js("window.location.href = '/{$path}'");
    }
}
