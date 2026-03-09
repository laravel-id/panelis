<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Services\Database\Contracts\Database;
use BackedEnum;
use Composer\InstalledVersions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class About extends Page
{
    use InteractsWithInfolists;

    protected string $view = 'filament.clusters.settings.pages.about';

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 90;

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
        $database = app(Database::class);

        return $schema
            ->schema([
                Section::make(__('setting.about.label'))
                    ->schema([
                        TextEntry::make('php_version')
                            ->label(__('setting.about.php_version'))
                            ->size(TextSize::Large)
                            ->state(phpversion()),

                        TextEntry::make('laravel_version')
                            ->label(__('setting.about.laravel_version'))
                            ->size(TextSize::Large)
                            ->state(ltrim(InstalledVersions::getPrettyVersion('laravel/framework'), 'v')),

                        TextEntry::make('filament_version')
                            ->label(__('setting.about.filament_version'))
                            ->size(TextSize::Large)
                            ->state(ltrim(InstalledVersions::getPrettyVersion('filament/filament'), 'v')),

                        TextEntry::make('database_version')
                            ->label(__('setting.about.database_version'))
                            ->size(TextSize::Large)
                            ->state(vsprintf('%s - %s', [
                                $database->getDriver()->getLabel(),
                                $database->getVersion(),
                            ])),
                    ]),
            ]);
    }
}
