<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\CountryResource\Enums\CountryPermission;
use App\Filament\Resources\Location\CountryResource\Forms\CountryForm;
use App\Filament\Resources\Location\CountryResource\Pages\ManageCountries;
use App\Models\Location\Country;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.location');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.country');
    }

    public static function getLabel(): ?string
    {
        return __('location.country');
    }

    public static function canAccess(): bool
    {
        return user_can(CountryPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.location', false) && self::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components(CountryForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('location.country_is_active'))
                    ->visible(user_can(CountryPermission::Edit)),

                TextColumn::make('name')
                    ->label(__('location.country_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('alpha2')
                    ->label(__('location.country_alpha2')),

                TextColumn::make('alpha3')
                    ->label(__('location.country_alpha2')),

                TextColumn::make('un_code')
                    ->label(__('location.country_un_code')),

                TextColumn::make('updated_at')
                    ->label(__('ui.updated_at'))
                    ->since(get_timezone())
                    ->dateTimeTooltip(get_datetime_format(), get_timezone())
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('location.country_is_visible')),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(user_can(CountryPermission::Edit)),

                DeleteAction::make()
                    ->visible(user_can(CountryPermission::Delete))
                    ->modalDescription(__('location.country_delete_confirmation')),
            ])
            ->toolbarActions([
                BulkAction::make('toggle')
                    ->label(__('location.country_toggle_status'))
                    ->visible(user_can(CountryPermission::Delete))
                    ->color('primary')
                    ->icon(Heroicon::CheckCircle)
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = ! $record->is_active;
                            $record->save();
                        }
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(user_can(CountryPermission::Delete))
                        ->modalDescription(__('location.country_delete_confirmation')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCountries::route('/'),
        ];
    }
}
