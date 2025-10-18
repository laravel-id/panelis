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
        return __('location.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('location.country.navigation');
    }

    public static function getLabel(): ?string
    {
        return __('location.country.label');
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
            ->components(CountryForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('location.country.is_active'))
                    ->visible(user_can(CountryPermission::Edit)),

                TextColumn::make('name')
                    ->label(__('location.country.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('alpha2')
                    ->label(__('location.country.alpha2')),

                TextColumn::make('alpha3')
                    ->label(__('location.country.alpha2')),

                TextColumn::make('un_code')
                    ->label(__('location.country.un_code')),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('location.country.is_visible')),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(user_can(CountryPermission::Edit)),

                DeleteAction::make()
                    ->visible(user_can(CountryPermission::Delete)),
            ])
            ->toolbarActions([
                BulkAction::make('toggle')
                    ->label(__('location.btn.toggle_status'))
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
                        ->visible(user_can(CountryPermission::Delete)),
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
