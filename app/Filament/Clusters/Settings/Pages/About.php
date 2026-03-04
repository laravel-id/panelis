<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use BackedEnum;
use Composer\InstalledVersions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class About extends Page
{
    use InteractsWithInfolists;

    protected string $view = 'filament.clusters.settings.pages.about';

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 100;

    public function getTitle(): string|Htmlable
    {
        return __('setting.about.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.about.navigation');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('setting.about.label'))
                    ->schema([
                        TextEntry::make('php_version')
                            ->label(__('setting.about.php_version'))
                            ->state(phpversion()),

                        TextEntry::make('laravel_version')
                            ->label(__('setting.about.laravel_version'))
                            ->state(InstalledVersions::getPrettyVersion('laravel/framework')),

                        TextEntry::make('filament_version')
                            ->label(__('setting.about.filament_version'))
                            ->state(InstalledVersions::getPrettyVersion('filament/filament')),
                    ]),
            ]);
    }
}
