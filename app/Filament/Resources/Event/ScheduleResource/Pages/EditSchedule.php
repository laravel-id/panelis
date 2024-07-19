<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Filament\Resources\Event\ScheduleResource;
use App\Models\URL\ShortURL;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['description'])) {
            $data['description'] = '';
        }

        $data['alias'] = Str::ascii($data['title']);

        return $data;
    }

    protected function afterSave(): void
    {
        $exists = ShortURL::query()
            ->where('destination_url', $this->record->url)
            ->exists();

        if (! $exists) {
            URLShortener::destinationUrl($this->record->url)
                ->trackVisits()
                ->make();
        }
    }
}
