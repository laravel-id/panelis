<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location;
use App\Models\Location\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function getNavigationGroup(): ?string
    {
        return __('location.navigation');
    }

    public static function getNavigationLabel(): string
    {
        return __('location.region');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-m-map';
    }

    public static function getLabel(): ?string
    {
        return __('location.region');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View region') && config('modules.location');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id')
                    ->label(__('location.country'))
                    ->relationship('country', 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->disabled(! Auth::user()->can('Create country'))
                            ->label(__('location.fields.name'))
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label(__('location.fields.name'))
                    ->required()
                    ->minLength(3)
                    ->maxLength(150),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update region');
        $canDelete = Auth::user()->can('Delete region');

        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('location.fields.is_active'))
                    ->visible($canUpdate),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('location.fields.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('location.country'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->sortable()
                    ->tooltip(fn (?Model $record): string => $record->updated_at ?? '')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('location.fields.is_active')),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label(__('location.country'))
                    ->relationship('country', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->searchable(),
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
                    ->color('primary')
                    ->icon('heroicon-m-check-circle')
                    ->visible($canUpdate)
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
            'index' => Location\RegionResource\Pages\ManageRegions::route('/'),
        ];
    }
}
