<?php

namespace App\Filament\Resources\TranslationResource\Pages;

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
        $data['text'] = collect($data['text'])
            ->mapWithKeys(fn (array $text): array => [$text['lang'] => $text['line']])
            ->toArray();

        return $data;
    }
}
