<?php

namespace App\Filament\Resources\Location;

use App\Models\Location\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.location');
    }

    public static function getNavigationLabel(): string
    {
        return __('location.country');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-m-flag';
    }

    public static function getLabel(): ?string
    {
        return __('location.country');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View country') && config('modules.location');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\TextInput::make('alpha2')
                    ->label(__('location.fields.alpha2'))
                    ->length(2),

                Forms\Components\TextInput::make('alpha3')
                    ->length(3)
                    ->label(__('location.fields.alpha3')),

                Forms\Components\TextInput::make('un_code')
                    ->label(__('location.fields.un_code'))
                    ->numeric()
                    ->length(3),

                Forms\Components\TextInput::make('name')
                    ->label(__('location.fields.name'))
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update country');
        $canDelete = Auth::user()->can('Delete country');

        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('location.fields.is_active'))
                    ->visible($canUpdate),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('location.fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('alpha2')
                    ->label(__('location.fields.alpha2')),

                Tables\Columns\TextColumn::make('alpha3')
                    ->label(__('location.fields.alpha2')),

                Tables\Columns\TextColumn::make('un_code')
                    ->label(__('location.fields.un_code')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->sortable()
                    ->tooltip(fn (?Model $record): string => $record->updated_at ?? '')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('location.fields.is_visible')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible($canUpdate),

                Tables\Actions\DeleteAction::make()
                    ->visible($canDelete)
                    ->modalDescription(__('location.delete_confirmation')),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('toggle')
                    ->label(__('location.toggle_status'))
                    ->visible($canUpdate)
                    ->color('primary')
                    ->icon('heroicon-m-check-circle')
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = ! $record->is_active;
                            $record->save();
                        }
                    }),

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible($canDelete)
                        ->modalDescription(__('location.delete_confirmation')),
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
