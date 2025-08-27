<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationResource\Enums\TranslationPermission;
use App\Filament\Resources\TranslationResource\Forms\TranslationForm;
use App\Filament\Resources\TranslationResource\Pages;
use App\Models\Translation;
use Filament\Forms\Components\Section;
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

    public static function canAccess(): bool
    {
        return user_can(TranslationPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema(TranslationForm::make()),
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
                    ->searchable(['key', 'text', 'group']),

                TextColumn::make(sprintf('text.%s', config('app.locale')))
                    ->label(__('translation.text')),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
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
                EditAction::make()
                    ->visible(user_can(TranslationPermission::Edit)),
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
