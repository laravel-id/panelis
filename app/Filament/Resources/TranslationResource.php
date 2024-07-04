<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationResource\Pages;
use App\Models\Translation;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Spatie\TranslationLoader\LanguageLine;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 2;

    public static function getLabel(): string
    {
        return __('translation.translation');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.translation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.system');
    }

    public static function form(Form $form): Form
    {
        $groups = Translation::orderBy('group')
            ->groupBy('group')
            ->pluck('group')
            ->toArray();

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('group')
                            ->label(__('translation.group'))
                            ->autocomplete(false)
                            ->datalist($groups)
                            ->helperText(function (?string $operation, ?Translation $line): ?string {
                                if ($operation === 'edit' && ! $line->is_system) {
                                    return __('translation.group_change_warning');
                                }

                                return null;
                            })
                            ->disabled(fn (?Translation $line): bool => $line?->is_system ?? false)
                            ->required()
                            ->alphaDash(),

                        TextInput::make('key')
                            ->label(__('translation.key'))
                            ->helperText(function (?string $operation, ?Translation $line): ?string {
                                if ($operation === 'edit' && ! $line->is_system) {
                                    return __('translation.key_change_warning');
                                }

                                return null;
                            })
                            ->autocomplete(false)
                            ->disabled(fn (?Translation $line): bool => $line?->is_system ?? false)
                            ->alphaDash()
                            ->required(),

                        KeyValue::make('text')
                            ->label(__('translation.text'))
                            ->addActionLabel(__('translation.add_line'))
                            ->keyLabel(__('translation.lang'))
                            ->valueLabel(__('translation.line'))
                            ->required(),
                    ]),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('group')
            ->defaultSort('key')
            ->columns([
                TextColumn::make('key')
                    ->label(__('translation.key'))
                    ->copyable()
                    ->sortable()
                    ->grow(false)
                    ->searchable(['key', 'text']),

                TextColumn::make(sprintf('text.%s', config('app.locale')))
                    ->label(__('translation.text')),

                TextColumn::make('local_updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->multiple()
                    ->options(function (): array {
                        return LanguageLine::select('group')
                            ->groupBy('group')
                            ->orderBy('group')
                            ->pluck('group', 'group')
                            ->toArray();
                    }),

                TernaryFilter::make('is_system')
                    ->label(__('translation.is_system')),
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
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
