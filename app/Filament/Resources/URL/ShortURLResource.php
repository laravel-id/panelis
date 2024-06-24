<?php

namespace App\Filament\Resources\URL;

use App\Filament\Resources\URL\ShortURLResource\Pages;
use App\Models\URL\ShortURL;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ShortURLResource extends Resource
{
    protected static ?string $model = ShortURL::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function getLabel(): ?string
    {
        return __('url.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.url');
    }

    public static function getSlug(): string
    {
        return 'url/short';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('url.short_url'))
                    ->description(__('url.short_url_section_description'))
                    ->schema([
                        TextInput::make('destination_url')
                            ->label(__('url.destination_url'))
                            ->live(onBlur: true)
                            ->url()
                            ->required(),

                        Toggle::make('single_use')
                            ->label(__('url.single_use'))
                            ->default(false),

                        Toggle::make('track_visits')
                            ->label(__('url.track_visit'))
                            ->default(true),

                        DateTimePicker::make('deactivated_at')
                            ->label(__('url.expired_at'))
                            ->native(false)
                            ->timezone(config('app.datetime_timezone', config('app.timezone')))
                            ->nullable(),

                        TextInput::make('url_key')
                            ->label(__('url.key'))
                            ->helperText(__('url.helper_key'))
                            ->prefix(function (): string {
                                $url = vsprintf('%s/%s/', [
                                    config('app.url'),
                                    config('short-url.prefix'),
                                ]);

                                return rtrim($url, '/').'/';
                            })
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),
                    ]),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        $timezone = config('app.datetime_timezone', config('app.timezone'));

        return $table
            ->columns([
                IconColumn::make('single_use')
                    ->label(__('url.single_use'))
                    ->boolean(),

                IconColumn::make('track_visits')
                    ->label(__('url.track_visit'))
                    ->boolean(),

                TextColumn::make('default_short_url')
                    ->label(__('url.short_url'))
                    ->copyable()
                    ->searchable(),

                TextColumn::make('destination_url')
                    ->label(__('url.destination_url'))
                    ->copyable()
                    ->searchable()
                    ->limit(50),

                TextColumn::make('deactivated_at')
                    ->label(__('url.expired_at'))
                    ->sortable()
                    ->since($timezone),

                TextColumn::make('created_at')
                    ->label(__('ui.created_at'))
                    ->sortable()
                    ->since($timezone),
            ])
            ->filters([
                TernaryFilter::make('single_use')
                    ->label(__('url.single_use')),

                TernaryFilter::make('track_visits')
                    ->label(__('url.track_visit')),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShortURL::route('/'),
            'create' => Pages\CreateShortURL::route('/create'),
            'edit' => Pages\EditShortURL::route('/{record}/edit'),
        ];
    }
}
