<?php

namespace App\Filament\Resources\Event;

use App\Filament\Resources\Event\OrganizerResource\Forms\OrganizerForm;
use App\Filament\Resources\Event\OrganizerResource\Pages;
use App\Models\Event\Organizer;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class OrganizerResource extends Resource
{
    protected static ?string $model = Organizer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.event');
    }

    public static function getLabel(): ?string
    {
        return __('event.organizer');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(OrganizerForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([20, 50, 100, 'all'])
            ->columns([
                Split::make([
                    ImageColumn::make('logo')
                        ->label(__('event.organizer_logo'))
                        ->disk('public')
                        ->grow(false)
                        ->circular(),

                    TextColumn::make('name')
                        ->label(__('event.organizer_name'))
                        ->weight(FontWeight::Bold)
                        ->sortable()
                        ->searchable()
                        ->description(fn (Organizer $organizer): ?string => Str::words($organizer->description, 10)),
                ]),

                Panel::make([
                    Stack::make([
                        TextColumn::make('phone')
                            ->icon('heroicon-m-phone')
                            ->copyable(),

                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),

                        TextColumn::make('website')
                            ->icon('heroicon-m-link')
                            ->formatStateUsing(function (Organizer $organizer): ?Htmlable {
                                if (empty($organizer->website)) {
                                    return null;
                                }

                                return Str::of(sprintf('[%s](%s)', $organizer->website, $organizer->website))
                                    ->inlineMarkdown()
                                    ->toHtmlString();
                            }),

                        TextColumn::make('address')
                            ->icon('heroicon-m-map-pin'),
                    ])->space(1),
                ])
                    ->collapsible()
                    ->visible(function (?Organizer $organizer): bool {
                        return ! empty($organizer->phone)
                            || ! empty($organizer->email)
                            || ! empty($organizer->website)
                            || ! empty($organizer->address);
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([

            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
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
            'index' => Pages\ListOrganizers::route('/'),
            'create' => Pages\CreateOrganizer::route('/create'),
            'edit' => Pages\EditOrganizer::route('/{record}/edit'),
        ];
    }
}
