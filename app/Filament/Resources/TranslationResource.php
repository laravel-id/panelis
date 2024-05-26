<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationResource\Pages;
use App\Models\Translation;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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

    public static function getModelLabel(): string
    {
        return __('translation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('group')
                            ->label(__('translation.group'))
                            ->helperText(function (?string $operation, ?Translation $line): ?string {
                                if ($operation === 'edit' && ! $line->is_system) {
                                    return __('translation.group_change_warning');
                                }

                                return null;
                            })
                            ->disabled(fn (?Translation $line): bool => $line?->is_system ?? false)
                            ->required(),

                        TextInput::make('key')
                            ->label(__('translation.key'))
                            ->helperText(function (?string $operation, ?Translation $line): ?string {
                                if ($operation === 'edit' && ! $line->is_system) {
                                    return __('translation.key_change_warning');
                                }

                                return null;
                            })
                            ->disabled(fn (?Translation $line): bool => $line?->is_system ?? false)
                            ->required(),

                        KeyValue::make('text')
                            ->label(__('translation.text'))
                            ->addActionLabel(__('translation.add_line'))
                            ->keyLabel(__('translation.lang'))
                            ->valueLabel(__('translation.line')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('group')
            ->paginated(false)
            ->columns([
                TextColumn::make('key')
                    ->label(__('translation.key'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(sprintf('text.%s', config('app.locale'))),

                TextColumn::make('updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->multiple()
                    ->options(function (): array {
                        return LanguageLine::select('group')
                            ->groupBy('group')
                            ->orderBy('group')
                            ->get()
                            ->mapWithKeys(function ($line): array {
                                $text = __(sprintf('%s.title', $line->group));

                                return [$line->group => sprintf('%s (%s)', $line->group, $text)];
                            })
                            ->toArray();
                    }),

                TernaryFilter::make('is_system')
                    ->label(__('translation.is_system')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
