<?php

namespace App\Filament\Resources\URL\ShortURLResource\Pages;

use App\Filament\Resources\URL\ShortURLResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditShortURL extends EditRecord
{
    protected static string $resource = ShortURLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($record->url_key !== $data['url_key']) {
            $data['default_short_url'] = str_replace($record->url_key, $data['url_key'], $record->default_short_url);
        }

        $record->update($data);

        return $record;
    }
}
