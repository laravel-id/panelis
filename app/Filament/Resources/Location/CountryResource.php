<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\CountryResource\Forms\CountryForm;
use App\Models\Location\Country;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
        return Auth::user()->can('ViewCountryLocation');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.location', false);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(CountryForm::make());
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('UpdateCountryLocation');
        $canDelete = Auth::user()->can('DeleteCountryLocation');

        return $table
            ->paginated(false)
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('location.country_is_active'))
                    ->visible($canUpdate),

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

                TextColumn::make('local_updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('location.country_is_visible')),
            ])
            ->actions([
                EditAction::make()
                    ->visible($canUpdate),

                DeleteAction::make()
                    ->visible($canDelete)
                    ->modalDescription(__('location.country_delete_confirmation')),
            ])
            ->bulkActions([
                BulkAction::make('toggle')
                    ->label(__('location.country_toggle_status'))
                    ->visible($canUpdate)
                    ->color('primary')
                    ->icon('heroicon-m-check-circle')
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = ! $record->is_active;
                            $record->save();
                        }
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible($canDelete)
                        ->modalDescription(__('location.country_delete_confirmation')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => CountryResource\Pages\ManageCountries::route('/'),
        ];
    }
}
