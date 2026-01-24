<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Actions\Translation\MutateText;
use App\Filament\Resources\TranslationResource;
use App\Filament\Resources\TranslationResource\Enums\TranslationPermission;
use Filament\Resources\Pages\CreateRecord;
use Symfony\Component\HttpFoundation\Response;

class CreateTranslation extends CreateRecord
{
    protected static string $resource = TranslationResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(TranslationPermission::Add), Response::HTTP_FORBIDDEN);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['text'] = MutateText::run($data['text']);

        return $data;
    }
}
