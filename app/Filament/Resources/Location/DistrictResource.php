<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location;
use App\Models\Location\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('location.navigation');
    }

    public static function getNavigationLabel(): string
    {
        return __('location.district');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-m-map-pin';
    }

    public static function getLabel(): ?string
    {
        return __('location.district');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View district') && config('modules.location');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('region_id')
                    ->label(__('location.region'))
                    ->relationship('region', 'name')
                    ->createOptionForm([
                        Forms\Components\Select::make('country_id')
                            ->label(__('location.country'))
                            ->disabled(! Auth::user()->can('Create region'))
                            ->relationship('country', 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('location.fields.name'
                                    ))
                                    ->disabled(! Auth::user()->can('Create country'))
                                    ->required()
                                    ->minLength(5)
                                    ->maxLength(150),
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('location.fields.name')
                            ->disabled(! Auth::user()->can('Create region'))
                            ->required(),
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('location.fields.name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(150),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update district');
        $canDelete = Auth::user()->can('Delete district');

        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('location.fields.is_active'))
                    ->visible($canUpdate),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('location.fields.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.name')
                    ->label(__('location.region'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.country.name')
                    ->label(__('location.country'))
                    ->sortable(),

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
                    ->relationship('region.country', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('region_id')
                    ->label(__('location.region'))
                    ->relationship('region', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible($canUpdate),

                Tables\Actions\DeleteAction::make()
                    ->visible($canDelete),
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
                        ->visible($canDelete),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Location\DistrictResource\Pages\ManageDistricts::route('/'),
        ];
    }
}
