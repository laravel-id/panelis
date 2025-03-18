<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Filament\Resources\TranslationResource;
use App\Filament\Resources\TranslationResource\Enums\TranslationPermission;
use App\Models\Translation;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Symfony\Component\HttpFoundation\Response;

class EditTranslation extends EditRecord
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(CreateTranslation::getUrl())
                ->visible(user_can(TranslationPermission::Add)),

            DeleteAction::make()
                ->visible(fn (?Translation $line): bool => ! $line->is_system && user_can(TranslationPermission::Delete)),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(TranslationPermission::Add), Response::HTTP_FORBIDDEN);
    }
}
