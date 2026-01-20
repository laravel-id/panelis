<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Filament\Resources\TranslationResource;
use App\Filament\Resources\TranslationResource\Enums\TranslationPermission;
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
                ->visible(user_can(TranslationPermission::Delete)),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(TranslationPermission::Add), Response::HTTP_FORBIDDEN);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['text'] = collect($data['text'])
            ->mapWithKeys(fn (array $text): array => [$text['lang'] => $text['line']])
            ->toArray();

        return $data;
    }
}
