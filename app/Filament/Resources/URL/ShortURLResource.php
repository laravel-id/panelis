<?php

namespace App\Filament\Resources\URL;

use App\Filament\Resources\URL\ShortURLResource\Pages;
use App\Models\URL\ShortURL;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

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
                            ->seconds(false)
                            ->timezone(get_timezone())
                            ->nullable()
                            ->minDate(now(get_timezone())),

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

                        Placeholder::make('short_url')
                            ->label(__('url.short_url'))
                            ->visibleOn(Pages\EditShortURL::class)
                            ->content(function (ShortURL $url): Htmlable {
                                return Str::of(sprintf('[%s](%s)', $url->default_short_url, $url->default_short_url))
                                    ->inlineMarkdown()
                                    ->toHtmlString();
                            }),
                    ]),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('single_use')
                    ->label(__('url.single_use'))
                    ->boolean(),

                ToggleColumn::make('track_visits')
                    ->label(__('url.track_visit')),

                TextColumn::make('url_key')
                    ->label(__('url.short_url'))
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage(__('url.short_url_copied'))
                    ->copyableState(fn (ShortURL $url): string => $url->default_short_url)
                    ->description(function (ShortURL $url): string {
                        return Str::limit($url->destination_url, 40);
                    })
                    ->searchable(['destination_url', 'url_key', 'default_short_url']),

                TextColumn::make('visits_count')
                    ->counts('visits')
                    ->label(__('url.total_visit'))
                    ->sortable()
                    ->badge(),

                TextColumn::make('deactivated_at')
                    ->label(__('url.expired_at'))
                    ->sortable()
                    ->since(get_timezone()),

                TextColumn::make('created_at')
                    ->label(__('ui.created_at'))
                    ->sortable()
                    ->since(get_timezone()),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
