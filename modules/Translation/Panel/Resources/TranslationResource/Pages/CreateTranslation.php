<?php

namespace Modules\Translation\Panel\Resources\TranslationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Translation\Actions\MutateText;
use Modules\Translation\Panel\Resources\TranslationResource;
use Modules\Translation\Panel\Resources\TranslationResource\Enums\TranslationPermission;
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
