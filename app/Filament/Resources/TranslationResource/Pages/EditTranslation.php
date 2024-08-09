<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Filament\Resources\TranslationResource;
use App\Models\Translation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EditTranslation extends EditRecord
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(CreateTranslation::getUrl())
                ->visible(Auth::user()->can('CreateTranslation')),

            Actions\DeleteAction::make()
                ->visible(fn (?Translation $line): bool => ! $line->is_system && Auth::user()->can('DeleteTranslation')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()->can('EditTranslation'), Response::HTTP_FORBIDDEN);
    }
}
