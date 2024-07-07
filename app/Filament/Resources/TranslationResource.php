<?php

namespace App\Filament\Resources;

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
use Illuminate\Support\Facades\Auth;
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

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('ViewTranslation');
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
                EditAction::make()
                    ->visible(Auth::user()->can('EditTranslation')),
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
