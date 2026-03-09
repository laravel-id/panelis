<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use Composer\InstalledVersions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Setting\Panel\Clusters\Settings;

class About extends Page
{
    use InteractsWithInfolists;

    protected string $view = 'filament.clusters.settings.pages.about';

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 90;

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.about.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.about.navigation');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('setting::setting.about.label'))
                    ->schema([
                        TextEntry::make('php_version')
                            ->label(__('setting::setting.about.php_version'))
                            ->state(phpversion()),

                        TextEntry::make('laravel_version')
                            ->label(__('setting::setting.about.laravel_version'))
                            ->state(InstalledVersions::getPrettyVersion('laravel/framework')),

                        TextEntry::make('filament_version')
                            ->label(__('setting::setting.about.filament_version'))
                            ->state(InstalledVersions::getPrettyVersion('filament/filament')),
                    ]),
            ]);
    }
}
